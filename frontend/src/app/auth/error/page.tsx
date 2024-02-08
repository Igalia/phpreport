'use client'
import { useSearchParams } from 'next/navigation'

export default function Error(){
    const searchParams = useSearchParams()
    const errorType = searchParams.get('error') ?? "1";
    let message = "";

    switch(errorType){
        case "Configuration":
            message = "There is a problem with the server configuration"
            break;
        case "AccessDenied":
            message = "Access denied. You do not have permission to use the application."
            break;
        default:
            message = "That was unexpected. Please contact an admin for assistance."
    }

    return (
        <>
            <h1>Something went wrong</h1>
            <p>
                {message}
            </p>
        </>
    )
}
