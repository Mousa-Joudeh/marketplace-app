import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { createBrowserRouter, redirect, RouterProvider } from 'react-router'
import './index.css'
import App from './App.jsx'
import { getAdminSession } from './api/adminAuth.js'
import AdminDashboard, { AdminDashboardError } from './pages/admin/AdminDashboard.jsx'
import AdminLogin from './pages/admin/AdminLogin.jsx'

const router = createBrowserRouter([
  { path: '/', Component: App },
  { path: '/admin/login', Component: AdminLogin },
  {
    path: '/admin/dashboard',
    Component: AdminDashboard,
    ErrorBoundary: AdminDashboardError,
    async loader({ request }) {
      try {
        const response = await getAdminSession({ signal: request.signal })
        return await response.json()
      } catch (error) {
        if (error.status === 401 || error.status === 403) {
          return redirect('/admin/login')
        }
        throw error
      }
    },
  },
])

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <RouterProvider router={router} />
  </StrictMode>,
)
