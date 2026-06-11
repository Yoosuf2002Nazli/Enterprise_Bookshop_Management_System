# Chapter 8: Limitations & Future Improvements

## 8.1 Limitations of the Current Implementation
While the Bookshop Management System demonstrates a working microservice architecture, it contains some design limitations accepted due to the educational scope of the project:

1. **Single-Threaded Dev Server (`php -S`):** PHP's built-in web server runs on a single thread. If one service blocks or is delayed, other queued requests are paused, making it unsuitable for production.
2. **Session Persistence Location:** Authentications rely on local PHP sessions on the frontend. A production system would use stateless token standards (JWT) passed in request headers to allow backend validation.
3. **Application-Level Relational Integrity:** Relational logic is resolved in code, meaning database consistency could drift if a service fails mid-checkout.
4. **Local Database Connections:** Database configuration keys are written directly into shared source code templates instead of external environment variable files (`.env`).

## 8.2 Future Roadmap Recommendations
To transition this project to a production-grade system, we recommend implementing the following improvements:

1. **API Gateway Aggregator:** Introduce an API Gateway (e.g. Kong, NGINX) to expose a unified host address, handling client request routing automatically.
2. **Stateless JWT Authorization:** Implement JWT validation inside each microservice router to ensure secure, cryptographically validated identity verification.
3. **Docker Containerization:** Package all services and databases into Docker containers, facilitating deployment and scaling across cloud infrastructure.
4. **State Consistency (Sagas):** Implement Saga orchestrators to manage rollback logic, ensuring database updates are reversed if a multi-service transaction fails mid-way.
