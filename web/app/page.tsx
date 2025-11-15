import { createClient } from '@supabase/supabase-js'

async function getAnnouncements() {
  const publicUrl = process.env.NEXT_PUBLIC_SUPABASE_URL as string | undefined
  const publicKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY as string | undefined
  const serverUrl = process.env.SUPABASE_URL as string | undefined
  const serverKey = process.env.SUPABASE_SERVICE_ROLE_KEY as string | undefined

  const url = publicUrl ?? serverUrl
  const key = publicKey ?? serverKey
  if (!url || !key) return []

  const supabase = createClient(url, key)
  const { data } = await supabase
    .from('announcements')
    .select('*')
    .order('posted_date', { ascending: false })
  return data ?? []
}

export default async function Page() {
  const announcements = await getAnnouncements()
  return (
    <main style={{ padding: 24, fontFamily: 'system-ui, sans-serif' }}>
      <h1>Supabase Eldera Announcements</h1>
      <p style={{ marginTop: 8 }}>
        <a href="/ims" style={{ color: '#2563eb', textDecoration: 'underline' }}>Open IMS</a>
      </p>
      {announcements.length === 0 && <p>No announcements.</p>}
      <ul style={{ listStyle: 'none', padding: 0 }}>
        {announcements.map((a: any) => (
          <li key={a.id} style={{ marginBottom: 16, padding: 12, border: '1px solid #ddd', borderRadius: 8 }}>
            <div style={{ fontWeight: 600 }}>{a.title}</div>
            <div style={{ color: '#666', fontSize: 14 }}>{a.category} · {a.department}</div>
            <div style={{ marginTop: 6 }}>{a.what}</div>
            <div style={{ marginTop: 6, fontSize: 14 }}>When: {a.when_event}</div>
            <div style={{ fontSize: 14 }}>Where: {a.where_location}</div>
            <div style={{ fontSize: 12, color: '#999' }}>Posted: {a.posted_date}</div>
          </li>
        ))}
      </ul>
    </main>
  )
}