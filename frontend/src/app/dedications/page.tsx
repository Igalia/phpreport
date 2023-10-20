import { getProjects } from '@/infra/project/getProjects'

export default async function Dedications() {
  const projects = await getProjects()

  return (
    <>
      <h1>This is the Dedications page</h1>
      <h2>Welcome</h2>
      <p>This is a sample React page that is using the FastApi backend to fetch some data.</p>
      <hr />
      <div>
        <h3>Projects from API</h3>
        {projects && projects.map((project) => <p key={project.id}>{project.description}</p>)}
      </div>
    </>
  )
}
