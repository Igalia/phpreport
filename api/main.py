from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from routers import projects

from db.db_connection import engine
from db.base_class import Base

Base.metadata.create_all(bind=engine)

app = FastAPI()

origins = [
    "http://localhost",
    "http://localhost:80",
    "https://localhost",
    "https://localhost:80",
    "https://localhost:8000",
    "http://localhost:8000",
    "http://localhost:5173",
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(projects.router)


@app.get("/")
async def root():
    return {"message": "Hello World"}
