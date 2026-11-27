import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router'
import { getAdminSession, logIn, logOut, verifyAdminSession, verifyTwoFactor } from '../../api/adminAuth.js'
import './AdminLogin.css'

export default function AdminLogin() {
  const navigate = useNavigate()
  const [step, setStep] = useState('checking-session')
  const [useRecoveryCode, setUseRecoveryCode] = useState(false)
  const [pending, setPending] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    let active = true

    getAdminSession().then(() => {
      if (active) void navigate('/admin/dashboard', { replace: true })
    }).catch((error) => {
      if (active) setStep(error.status === 403 ? 'account-blocked' : 'credentials')
    })

    return () => { active = false }
  }, [navigate])

  async function handleSubmit(event) {
    event.preventDefault()
    if (pending) return

    const fields = new FormData(event.currentTarget)
    setPending(true)
    setError('')

    try {
      if (step === 'challenge') {
        await verifyTwoFactor(Object.fromEntries(fields))
        setStep('credentials')
      } else {
        const result = await logIn(Object.fromEntries(fields))

        if (result.two_factor) {
          setStep('challenge')
          return
        }
      }

      await verifyAdminSession()
      await navigate('/admin/dashboard', { replace: true })
    } catch (error) {
      setError(error instanceof TypeError ? 'Could not connect to the server. Please try again.' : error.message)
    } finally {
      setPending(false)
    }
  }

  async function handleLogout() {
    setPending(true)
    setError('')

    try {
      await logOut()
      setUseRecoveryCode(false)
      setStep('credentials')
    } catch {
      setError('Could not log out. Please try again.')
    } finally {
      setPending(false)
    }
  }

  return (
    <main className="admin-login">
      <section className="admin-login__card" aria-labelledby="admin-login-title">
        <h1 id="admin-login-title">Admin Login</h1>
        {error && <p className="admin-login__error" role="alert">{error}</p>}

        {step === 'checking-session' && <p role="status">Checking session…</p>}

        {step === 'account-blocked' && (
          <>
            <p role="status">This account cannot access the admin area.</p>
            <button type="button" onClick={handleLogout} disabled={pending}>
              {pending ? 'Logging out…' : 'Log out'}
            </button>
          </>
        )}

        {(step === 'credentials' || step === 'challenge') && (
          <form key={step} onSubmit={handleSubmit} method="post" aria-busy={pending}>
            {step === 'challenge' ? (
              <>
                <label htmlFor="verification-code">
                  {useRecoveryCode ? 'Recovery code' : 'Authentication code'}
                </label>
                <input
                  key={useRecoveryCode}
                  id="verification-code"
                  name={useRecoveryCode ? 'recovery_code' : 'code'}
                  inputMode={useRecoveryCode ? 'text' : 'numeric'}
                  autoComplete={useRecoveryCode ? 'off' : 'one-time-code'}
                  required
                />
                <button
                  className="admin-login__switch"
                  type="button"
                  disabled={pending}
                  onClick={() => setUseRecoveryCode(!useRecoveryCode)}
                >
                  {useRecoveryCode ? 'Use authentication code' : 'Use a recovery code'}
                </button>
              </>
            ) : (
              <>
                <label htmlFor="email">Email</label>
                <input id="email" name="email" type="email" autoComplete="username" required />

                <label htmlFor="password">Password</label>
                <input id="password" name="password" type="password" autoComplete="current-password" required />
              </>
            )}

            <button type="submit" disabled={pending}>
              {pending ? 'Checking…' : step === 'challenge' ? 'Verify code' : 'Log in'}
            </button>
          </form>
        )}
      </section>
    </main>
  )
}
