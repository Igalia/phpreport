/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  async redirects() {
    return [
      {
        source: '/',
        destination: '/tasks',
        permanent: true
      }
    ]
  }
}

module.exports = nextConfig
