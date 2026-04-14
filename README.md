# PWP SPRING 2026
# Async-Linguist
# Group information
### Group Name: Async-4
* Student 1. Atif Bashir (Atif.Bashir@student.oulu.fi)
* Student 2. Muhammad Bilal (mbilal25@student.oulu.fi)
* Student 3. Muhammad Faizan Tanveer (Muhammad.Tanveer@student.oulu.fi)
* Student 4. Arshman Tariq (Arshman.Tariq@student.oulu.fi)


__Remember to include all required documentation and HOWTOs, including how to create and populate the database, how to run and test the API, the url to the entrypoint, instructions on how to setup and run the client, instructions on how to setup and run the axiliary service and instructions on how to deploy the api in a production environment__

### Why no logout route
Unlike traditional session-based authentication (where the server destroys a session cookie in the database), JWTs are stateless.

Because the token is stored purely on the frontend (usually in localStorage or memory), the backend does not actively track who is logged in.

Therefore, to "logout," you do not need a backend /auth/logout endpoint. The process is entirely handled by the client:

The user clicks "Logout" in the frontend UI.

The frontend application deletes the access_token from localStorage.

The user is instantly logged out because subsequent requests will lack the Bearer token.