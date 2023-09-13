'use client'

import { useState, useEffect } from 'react'
import { useAuth } from 'react-oidc-context'
import { apiClient } from '../../infra/apiClient'

type Project = {
  id: string
  description: string
}

export default function Dedications() {
  const auth = useAuth()
  const [projects, setProjects] = useState<Project[]>([])
  const username_prop = process.env.NEXT_PUBLIC_OIDC_USERNAME_PROPERTY || ''
  const username = auth.user?.profile[username_prop] as string

  useEffect(() => {
    const token = auth.user?.access_token

    if (token) {
      const getProjects = async () => {
        try {
          apiClient(token)
            .get('/v1/projects')
            .then(({ data }) => {
              setProjects(data)
            })
        } catch (e) {
          console.error(e)
        }
      }

      getProjects()
    }
  }, [auth.user?.access_token])

  return (
    <>
      <h1>This is the Dedications page</h1>
      <h2>Welcome {username}</h2>
      <p>This is a sample React page that is using the FastApi backend to fetch some data.</p>
      <hr />
      <div>
        <h3>Projects from API</h3>
        {projects && projects.map((project) => <p key={project.id}>{project.description}</p>)}
      </div>
    </>
  )
}
