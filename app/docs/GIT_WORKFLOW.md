# Git Workflow (Sprint Branching)

This document explains how we will use Git branches during the project. The goal is a clean workflow and one complete merged solution per sprint.

## Branch types we will use

### 1) `main`

* **Purpose:** Always contains the most stable, working version.
* **Rule:** Only merge into `main` when a sprint is finished and tested.
* **Extra:** After merging, create a tag like `v1.0`, `v1.1`, etc.

### 2) Sprint branches (`sprint-1`, `sprint-2`, `sprint-3`)

* **Purpose:** All work for that sprint is combined here.
* **Rule:** During the sprint, all features are merged into the sprint branch, not into `main`.
* **Important:** At the end of the sprint, the sprint branch must work as one complete solution, because the sprint is graded as one merged result.

### 3) Feature branches (`feature/login`, `feature/inventory-filter`, `feature/pdf-export`)

* **Purpose:** Small focused work, one feature at a time.
* **Rule:** A feature branch is created from the current sprint branch, then merged back into that sprint branch when finished.

## Workflow in practice

### Step 1: Start of sprint

1. Create the sprint branch from `main`.

    * Example: `sprint-1` is created from `main`.

### Step 2: Build features one by one

For each feature:

1. Create a feature branch from the sprint branch.

    * Example: `feature/feature-1-add-items` created from `sprint-1`.
2. Work only on that feature in the feature branch.
3. Commit often with clear messages.
4. When the feature is finished, tested, and builds successfully:

    * Merge `feature/feature-1-add-items` into `sprint-1`.
5. Delete the feature branch after merge (optional but recommended).

### Step 3: End of sprint (single merged solution)

Before finishing the sprint, make sure `sprint-1` contains:

* all merged features for that sprint
* code builds without errors
* tests pass (if you have them)
* the app runs correctly

Then:

1. Merge `sprint-1` into `main`.
2. Tag the result.

    * Example: `v1.1` (or `sprint-1-done`, depending on the team preference)

### Step 4: Start next sprint

1. Create `sprint-2` from the updated `main`.
2. Repeat the same process.

## Rules that keep it clean and avoid problems

* No direct work on `main`, only merges at the end of a sprint.
* No direct work on `sprint-x`, sprint branches should mainly receive merges from feature branches.
* One feature branch per feature, keep each branch small and focused.
* Merge often into the sprint branch, this reduces conflicts and keeps integration easy.

## Database migrations

Migrations can cause conflicts, so we will follow one clear rule, for example:

* only one person creates migrations, or
* migrations are created per feature, but merged quickly and tested together in the sprint branch

## Example branch naming

* `sprint-1`, `sprint-2`, `sprint-3`
* `feature/feature-1`
* `feature/inventory-filter`
* `feature/pdf-export`
* `bugfix/cart-total` (optional)
