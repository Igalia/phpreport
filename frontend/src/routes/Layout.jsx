import { Outlet, Link } from 'react-router-dom';

export default function Layout() {
  return (
    <>
      <main>
        <nav>
          <ul style={{ listStyleType: 'none' }}>
            <li style={{ display: 'inline', margin: '2em' }}>
              <Link to="/" reloadDocument>
                Home
              </Link>
            </li>
            <li style={{ display: 'inline', margin: '2em' }}>
              <Link to="/web/v2/dedications">Dedications</Link>
            </li>
          </ul>
        </nav>
        <Outlet />
      </main>
    </>
  );
}
