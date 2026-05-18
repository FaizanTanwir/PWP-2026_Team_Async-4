# Meetings minutes

## Meeting 1.
* **DATE:** 2026-02-03
* **PARTICIPANTS:** Arshman Tariq, Atif Bashir, Muhammad Bilal, Muhammad Faizan Tanveer
* **TEACHER:** Ivan Sanchez Milara

### Action points
*List here the actions points discussed with assistants*
+ Clarify how the system functions specifically as a REST API (not just general functionality).
+ Expand descriptions of main concepts (what data each concept contains and represents).
+ Improve explanation of API clients and services (describe them like real-world use cases, e.g., marketplace-style).
+ Justify REST classification better (focus on resources, not just endpoints and HTTP methods).
+ Refine the related work section with clearer comparison and proper API classification.

### Notes
*Add here notes that you consider important. This is not mandatory*
+ Concepts and diagrams were present but lacked depth in explanation.
+ API design leaned toward endpoints rather than resource-based thinking.
+ Good start, but needs clearer REST principles and stronger conceptual clarity.


## Meeting 2.
* **DATE:** 2026-02-17
* **PARTICIPANTS:** Arshman Tariq, Atif Bashir, Muhammad Bilal, Muhammad Faizan Tanveer
* **TEACHER:** Ivan Sanchez Milara

### Action points
*List here the actions points discussed with assistants*
+ Review and improve the sentence–word relationship, especially considering a proper many-to-many design without unnecessary model classes.
+ Double-check relationship design and ensure optimal normalisation.

### Notes
*Add here notes that you consider important. This is not mandatory*
+ Overall database design and implementation were strong.
+ A minor issue with the relationship modelling prevented full marks.
+ README and setup instructions were clear and complete.


## Meeting 3.
* **DATE:** 2026-03-09
* **PARTICIPANTS:** Arshman Tariq, Atif Bashir, Muhammad Bilal, Muhammad Faizan Tanveer
* **TEACHER:** Ivan Sanchez Milara

### Action points
*List here the actions points discussed with assistants*
+ Define proper resource hierarchy (avoid having all resources at root level).
+ Improve uniform interface by documenting methods for each individual resource, not just collections.
+ Add connectedness (HATEOAS) – include links between resources.
+ Add inline code documentation.
+ Complete and improve README instructions (setup, run, test).
+ Increase test coverage significantly.
+ Provide reference for NestJS project structure best practices.
+ Improve/clarify URL converter usage.
+ Justify caching decisions for all endpoints (not just languages).
+ Implement and document authentication.
+ Review and improve type annotations.

### Notes
*Add here notes that you consider important. This is not mandatory*
+ Implementation works but lacks completeness in documentation and testing.
+ Strong foundation, but many REST constraints are only partially satisfied.
+ Extras (authentication, implementation works, connectedness) deferred to next deliverable due to lack of time.


## Meeting 4. (Midterm)
* **DATE:** 2026-04-14
* **PARTICIPANTS:** Arshman Tariq, Atif Bashir, Muhammad Bilal, Muhammad Faizan Tanveer
* **TEACHER:** Ivan Sanchez Milara

### Action points
*List here the actions points discussed with assistants*
+ Improve documentation structure by adding reusable schemas and parameters (avoid overly basic global configuration).
+ Add missing response examples, especially for Words endpoints and error responses.
+ Include all relevant response codes:
     - Add missing 400 errors.
     - Add authentication-related response codes.
     - Fix incorrect response codes in Words endpoints.
+ Complete request body documentation, especially for Words resources.
+ Implement a monitoring/control system (e.g., supervisor or equivalent).
+ Enhance architecture diagram:
     - Include frameworks, ports, Docker setup, and communication details.
+ Use and justify a proper web server (e.g., Nginx/Apache) in front of the application server.
+ Expand explanation and configuration of deployment components.

### Notes
*Add here notes that you consider important. This is not mandatory*
+ Documentation is generally valid and covers endpoints well, but lacks depth in structure and completeness.
+ Major gaps are in error handling documentation and examples.
+ Deployment works and is accessible, but missing production-level components (monitoring, web server).
+ The architecture diagram needs to better reflect the real system setup and technical details.


## Final meeting
* **DATE:** 2026-05-18
* **PARTICIPANTS:** Arshman Tariq, Atif Bashir, Muhammad Bilal, Muhammad Faizan Tanveer
* **TEACHER:** Ivan Sanchez Milara

### Minutes
*Summary of what was discussed during the meeting*

##### Deadline 5 evaluation:
The client implementation received full marks (11/11). The Vue.js dashboard was demonstrated live, covering the teacher and student workflows, role-based rendering, quiz session management, and error handling. The usability, visuals, and complexity criteria were all satisfied.
The auxiliary service received partial marks (6.5/11). The main issue raised was that LibreTranslate is a third-party, pre-built service — the team integrated and called its existing API rather than implementing an auxiliary service from scratch. As a result, the Code Structure (1.0), API Implementation (2.5), and Code Quality (1.0) criteria received zero points, as these criteria require the students to have written the service code themselves.
The Idea (1.0), Overview (1.0), Communication Diagram (1.0), and Instructions (1.0) criteria were awarded full marks, confirming that the justification, documentation, and setup instructions were correct and well-written. The Demonstration (2.5) criterion was also awarded full marks as the LibreTranslate integration was shown working end-to-end during the session.
A minor deduction of 0.5 was applied to the Client Overview (0.5/1.0), as the description focused too much on the technology stack rather than the functionality and goals of the client from a user perspective.

##### Re-evaluation of Deadlines 1–4:
Deadlines 1–4 were re-assessed after the team updated all wiki pages to reflect the actual technology stack (Laravel 12, MySQL, Docker, Nginx Proxy Manager, Supervisor) and addressed the specific action points from each previous meeting. The improvements resulted in significantly higher scores across Deadlines 1, 3, and 4. Deadline 2 was already near full marks and received full marks in the final evaluation.

+ Deadline 1 improved from 3.2/5 to 4.7/5. The API description was clarified to focus on the system as an API component, the conceptual model was corrected to match the actual implementation, and the related work section was given a more rigorous REST classification using the Richardson Maturity Model.
+ Deadline 2 improved from 4.75/5 to 5.0/5. The sentence–word many-to-many relationship was documented correctly using Eloquent's belongsToMany without an intermediate model class.
+ Deadline 3 improved from 11/21 to 19.5/21. The resource table was expanded to show individual resources alongside collections, the uniform interface was documented per-resource rather than only for collections, connectedness links were added to response examples, and all extras (authentication, caching, URL converters) were updated to use correct Laravel-specific terminology.
+ Deadline 4 improved from 13.5/19 to 19.0/19. The tools table was updated to reflect the real stack, an architecture diagram with ports and communication protocols was added, Supervisor was documented as the process monitor, and the web server justification addressed Nginx Proxy Manager and php artisan serve explicitly.

### Action points
+ No further action points — this is the final submission.

### Notes
*Add here notes that you consider important. This is not mandatory*

+ The re-evaluation of Deadlines 1–4 showed significant improvement, particularly in Deadlines 3 and 4, where the team correctly addressed all prior feedback.
+ The auxiliary service deduction (5 marks lost) was the main gap in the final assessment. Using a pre-built third-party service satisfies the Idea and documentation criteria but does not meet the implementation criteria, which require student-written service code.
+ The client implementation was strong across all criteria — demonstration, error handling, usability, and complexity were all well-executed.
+ Overall project execution improved substantially from the midterm to the final submission.



