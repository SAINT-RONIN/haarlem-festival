# Code Conventions and Rules

This document describes the coding rules used in this project. The goal is to keep the code clean, readable, predictable, and easy to maintain as a team.

## General principles

* Code should be **simple and readable**.
* Each class and method should have **one clear responsibility**.
* Code should be written in a **reusable** way whenever possible.
* Separation of concerns must always be respected.

---

## Method length

* **Public and private methods should not be longer than 10 lines.**
* Breaking this rule is only allowed in specific situations:

    * There is genuinely no other reasonable way to shorten the method.
    * The method exceeds the limit slightly (for example 12–13 lines) and splitting it would reduce readability.

**Preferred solution:**

* If a method grows too long, extract part of the logic into a **private method**.
* Use judgement. Do not blindly split methods if it makes the code harder to understand.

---

## Responsibilities per layer

### Services

* Services must **only contain business logic**.
* Services may call repositories and use enums.
* Services must not:

    * Handle HTTP requests
    * Start sessions
    * Render views
    * Contain SQL queries

### Controllers

* Controllers must **not contain business logic**.
* Controllers are responsible for:

    * Handling requests
    * Calling services
    * Redirecting or loading views

### Views

* Views must only display data.
* Views must not contain:

    * Business logic
    * Inline CSS
    * Inline JavaScript
* Styling and scripts must live in their own asset files.

---

## Method naming rules

* Method names must clearly describe **what the method does**.
* A method must **only do what its name promises**.

**Bad example:**

* `checkPassword()` checks the password **and** starts a session.

**Good example:**

* `checkPassword()` only checks the password.
* `startSession()` handles session logic separately.

This avoids hidden side effects and unexpected behaviour.

---

## Why this matters (project example)

If a method such as `checkPassword()` also starts or re-initialises the session, this can cause serious issues.

Example problem:

* A user resets their password.
* `checkPassword()` is called.
* The method restarts the session internally.
* As a result, existing session data is lost.

Possible consequences:

* Shopping cart becomes empty.
* Selected tickets disappear.
* User loses progress during checkout.

These bugs are difficult to trace because the method name does not indicate that session logic is involved.

---

## Reusability

* Code should be written so it can be reused in other parts of the project.
* Avoid hardcoded values when a parameter or enum would be clearer.
* Do not tightly couple methods to one specific controller or flow.

Reusable code:

* Reduces duplication
* Makes testing easier
* Lowers the risk of bugs when features change

---

## Summary

* Keep methods short and focused.
* Respect MVC and separation of concerns.
* Do exactly what the method name promises.
* Avoid hidden side effects.
* Write reusable, predictable code.
