export async function getDashboardSummary(signal) {
  const response = await fetch('/api/admin/dashboard', {
    credentials: 'same-origin',
    headers: { Accept: 'application/json' },
    signal,
  })

  if (!response.ok) {
    const error = new Error('Could not load the dashboard overview.')
    error.status = response.status
    throw error
  }

  return response.json()
}
