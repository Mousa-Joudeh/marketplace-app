export default function AdminLogin() {
    function handleSubmit(event) {
        event.preventDefault();
    }

    return (
        <main>
            <h1>Admin Login</h1>

            <form onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autoComplete="username"
                        required
                    />
                </div>

                <div>
                    <label htmlFor="password">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autoComplete="current-password"
                        required
                    />
                </div>

                <button type="submit">Log in</button>
            </form>
        </main>
    );
}
