async function request(path, options = {}) {
  const response = await fetch(path, {
    ...options,
    credentials: 'same-origin',
    redirect: 'error',
    headers: { Accept: 'application/json', ...options.headers },
  })

  if (!response.ok) {
    const messages = {
      401: 'Could not verify your session. Please sign in again.',
      403: 'This account cannot access the admin area.',
      419: 'Your session expired. Please try again.',
      429: 'Too many attempts. Please wait and try again.',
    }
    let message = messages[response.status] ?? 'Could not complete the request. Please try again.'

    if (response.status === 422) {
      const body = await response.json().catch(() => null)
      message = body?.message ?? 'Check the information you entered and try again.'
    }

    const error = new Error(message)
    error.status = response.status
    throw error
  }

  return response
}

function post(path, body = {}) {
  const cookie = document.cookie.split('; ').find((item) => item.startsWith('XSRF-TOKEN='))

  if (!cookie) {
    throw new Error('Could not start a secure session. Please try again.')
  }

  return request(path, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-XSRF-TOKEN': decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)),
    },
    body: JSON.stringify(body),
  })
}

export async function logIn(credentials) {
  await request('/sanctum/csrf-cookie')
  const response = await post('/login', credentials)

  return response.json()
}

export function verifyTwoFactor(challenge) {
  return post('/two-factor-challenge', challenge)
}

export async function logOut() {
  await request('/sanctum/csrf-cookie')
  return post('/logout')
}

export function getAdminSession(options) {
  return request('/api/admin/me', options)
}

export async function verifyAdminSession() {
  await getAdminSession().catch(async (error) => {
    await logOut().catch(() => {})
    throw error
  })
}
