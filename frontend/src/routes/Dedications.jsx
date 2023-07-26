import { useState, useEffect } from 'react';
import { useAuth } from 'react-oidc-context';
import axios from '../utils/ApiClient';

export default function Dedications() {
  const auth = useAuth();
  const [projects, setProjects] = useState(null);
  const token = auth.user?.id_token;

  useEffect(() => {
    const getProjects = async () => {
      try {
        axios(token)
          .get('/projects')
          .then(({ data }) => {
            setProjects(data);
          });
      } catch (e) {
        console.error(e);
      }
    };

    getProjects();
  }, [auth]);

  return (
    <>
      <h1>This is the Dedications page</h1>
      <h2>Welcome {auth.user?.profile.sub}</h2>
      <p>This is a sample React page that is using the FastApi backend to fetch some data.</p>
      <hr />
      <div>
        <h3>Projects from API</h3>
        {projects && projects.map((project) => <p key={project.id}>{project.description}</p>)}
      </div>
    </>
  );
}
