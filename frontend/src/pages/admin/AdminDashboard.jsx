import { useEffect, useState } from 'react'
import { Link, useLoaderData, useNavigate } from 'react-router'
import { logOut } from '../../api/adminAuth.js'
import { getDashboardSummary } from '../../api/adminDashboard.js'
import './AdminDashboard.css'

const metrics = [
  { key: 'bookings', label: 'Bookings' },
  { key: 'spaces', label: 'Spaces' },
  { key: 'services', label: 'Services' },
  { key: 'pending_providers', label: 'Providers awaiting approval' },
]

const formatNumber = new Intl.NumberFormat('en-JO')
const formatDate = new Intl.DateTimeFormat('en-JO', { dateStyle: 'medium', timeZone: 'Asia/Amman' })

export function AdminDashboardError() {
  return (
    <main>
      <h1>Admin Dashboard</h1>
      <p role="alert">Could not verify the admin session. Please try again.</p>
      <Link to="/admin/login">Go to login</Link>
    </main>
  )
}

export default function AdminDashboard() {
  const admin = useLoaderData()
  const navigate = useNavigate()
  const [pending, setPending] = useState(false)
  const [logoutFailed, setLogoutFailed] = useState(false)
  const [summary, setSummary] = useState(null)
  const [loadFailed, setLoadFailed] = useState(false)
  const [requestVersion, setRequestVersion] = useState(0)

  useEffect(() => {
    const controller = new AbortController()

    getDashboardSummary(controller.signal).then((data) => {
      if (!controller.signal.aborted) setSummary(data)
    }).catch((error) => {
      if (controller.signal.aborted) return
      if (error.status === 401 || error.status === 403) {
        void navigate('/admin/login', { replace: true })
        return
      }
      setLoadFailed(true)
    })

    return () => controller.abort()
  }, [navigate, requestVersion])

  function retrySummary() {
    setLoadFailed(false)
    setRequestVersion((version) => version + 1)
  }

  async function handleLogout() {
    setPending(true)
    setLogoutFailed(false)

    try {
      await logOut()
      await navigate('/admin/login', { replace: true })
    } catch {
      setLogoutFailed(true)
    } finally {
      setPending(false)
    }
  }

  return (
    <div className="dashboard-shell">
      <aside className="dashboard-sidebar">
        <strong>Marketplace</strong>
        <nav aria-label="Admin navigation">
          <a href="#overview">Overview</a>
          <a href="#recent-bookings">Recent bookings</a>
        </nav>
      </aside>

      <main id="overview" className="dashboard-main">
        <header className="dashboard-header">
          <div>
            <h1>Admin Dashboard</h1>
            <p>Signed in as {admin.name}</p>
          </div>
          <button type="button" onClick={handleLogout} disabled={pending}>
            {pending ? 'Logging out…' : 'Log out'}
          </button>
        </header>

        {logoutFailed && <p role="alert">Could not log out. Please try again.</p>}
        {loadFailed ? (
          <div className="dashboard-feedback">
            <p role="alert">Dashboard data is currently unavailable.</p>
            <button type="button" onClick={retrySummary}>Retry</button>
          </div>
        ) : !summary && <p role="status">Loading dashboard data…</p>}

        <dl className="dashboard-metrics" aria-label="Marketplace totals">
          {metrics.map(({ key, label }) => (
            <div key={key} className="dashboard-metric">
              <dt>{label}</dt>
              <dd>{summary ? formatNumber.format(summary.metrics[key]) : '—'}</dd>
            </div>
          ))}
        </dl>

        <section id="recent-bookings" className="dashboard-panel" aria-labelledby="recent-bookings-title">
          <h2 id="recent-bookings-title">Recent bookings</h2>
          {summary?.recent_bookings.length > 0 ? (
            <div className="dashboard-table-scroll" tabIndex={0} role="region" aria-labelledby="recent-bookings-title">
              <table aria-labelledby="recent-bookings-title">
                <thead>
                  <tr><th scope="col">Booking</th><th scope="col">Status</th><th scope="col">Created</th></tr>
                </thead>
                <tbody>
                  {summary.recent_bookings.map((booking) => (
                    <tr key={booking.id}>
                      <th scope="row">{booking.reference}</th>
                      <td><span className="dashboard-booking-status">{booking.status.replaceAll('_', ' ')}</span></td>
                      <td><time dateTime={booking.created_at}>{formatDate.format(new Date(booking.created_at))}</time></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : <p>{summary ? 'No bookings yet.' : 'Recent bookings will appear when the data loads.'}</p>}
        </section>
      </main>
    </div>
  )
}
