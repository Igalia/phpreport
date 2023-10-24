'use client'

type ErrorProps = {
    error: Error;
}
export default function Error({error}: ErrorProps) {

    return (
        <>
            <h1>Something went wrong</h1>
            <p>{error?.message || ""}</p>
        </>
    )
}
