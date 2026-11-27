import { useState } from 'react'
import { Link, useLoaderData, useNavigate } from 'react-router'
import { logOut } from '../../api/adminAuth.js'

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
    <main>
      <h1>Admin Dashboard</h1>
      <p>Signed in as {admin.name}.</p>
      {logoutFailed && <p role="alert">Could not log out. Please try again.</p>}
      <button type="button" onClick={handleLogout} disabled={pending}>
        {pending ? 'Logging out…' : 'Log out'}
      </button>
    </main>
  )
}
