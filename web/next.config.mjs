/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  async rewrites() {
    const base = process.env.IMS_BASE_URL
    if (!base) return []
    return {
      beforeFiles: [
        {
          source: '/',
          destination: `${base}/Login`,
        },
      ],
      afterFiles: [
        {
          source: '/ims/:path*',
          destination: `${base}/:path*`,
        },
      ],
      fallback: [],
    }
  },
}

export default nextConfig