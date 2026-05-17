
# Linguist Frontend

Linguist is a sophisticated, AI-driven language learning and translation platform. This repository contains the modern, reactive frontend built with **Vue 3**, styled with **Tailwind CSS**, and optimized for a seamless user experience across devices.

---

## Tech Stack

- **Core Framework:** [Vue 3](https://vuejs.org/) (Composition API)
- **State Management:** [Pinia](https://pinia.vuejs.org/) (Modular & Reactive)
- **Routing:** [Vue Router](https://router.vuejs.org/) (SPA Architecture)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/) & [daisyUI v5](https://daisyui.com/)
- **Build Tool:** [Vite](https://vitejs.dev/)
- **Linting:** [ESLint v10+](https://eslint.org/) (Flat Config)

### Theming
The UI utilizes the latest **daisyUI v5** features to provide a polished aesthetic. We support high-contrast, accessible themes:
* **Light Mode:** `Corporate` (Professional, clean layout)
* **Dark Mode:** `Business` (Optimized for low-light environments)

---

## Prerequisites

To ensure stability and compatibility with our modern build tools, the following environment is required:

* **Node.js:** `v24.12.0` or higher
* **Package Manager:** `npm` (included with Node)

---

## Local Setup & Installation

Follow these steps to get your local development environment running:

### 1. Clone the Repository
```bash
git clone <repository-url>
cd frontend
```

### 2. Environment Configuration

The application requires a connection to the Linguist API.

1. Create a `.env` file in the root `frontend` folder.
2. Copy the contents from `.env.sample`.
3. Update the `VITE_API_URL` to point to your local or deployed API instance.

```bash
cp .env.sample .env
```

### 3. Install Dependencies

```bash
npm install
```

### 4. Run Development Server

```
npm run dev
```

The application will be available at `http://localhost:5173`.

---

## Code Quality & Linting

To maintain a clean codebase and prevent common bugs, we use **ESLint v10** with the new Flat Config system. We have integrated Vue-specific rules to ensure component consistency.

| Command | Description |
| --- | --- |
| `npm run lint --fix` | Automatically repairs fixable linting issues and formatting. |

---

## Deployment

Our frontend is automatically deployed and hosted via **Render**.

* **Environment:** Production
* **Build Command:** `npm run build`
* **Publish Directory:** `dist`
* **Routing:** Configured with specialized Rewrite Rules to support Vue Router's SPA history mode (redirecting all server requests to `index.html`).

---

## Project Structure

* `src/components/` - Reusable UI elements.
* `src/views/` - Main page components managed by Vue Router.
* `src/stores/` - Pinia state management modules.
* `src/assets/` - Global styles and static images.
* `src/utils/` - helper functions to be used throughout the frontend.
