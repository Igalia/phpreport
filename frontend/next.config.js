/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  async redirects() {
    return [
      {
        source: '/',
        destination: '/tasks/day',
        permanent: true
      },
      {
        source: '/tasks',
        destination: '/tasks/day',
        permanent: true
      }
    ]
  }
}

module.exports = nextConfig
