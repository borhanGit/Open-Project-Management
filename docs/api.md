# REST API Reference

OpenProject Laravel exposes an API for project management integrations, automation, and CI/CD triggers.

---

## Authentication

All API endpoints use Laravel Sanctum bearer tokens or standard session authentication:

```http
Authorization: Bearer <your-personal-access-token>
Accept: application/json
```

---

## Endpoints

### Projects

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/projects` | List active projects |
| `POST` | `/api/v1/projects` | Create a new project |
| `GET` | `/api/v1/projects/{identifier}` | Get project details |
| `PUT/PATCH` | `/api/v1/projects/{identifier}` | Update project |
| `DELETE` | `/api/v1/projects/{identifier}` | Delete / archive project |

### Work Packages (Tasks & Issues)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/projects/{identifier}/work-packages` | List work packages for a project (supports status, type, assignee filters) |
| `POST` | `/api/v1/projects/{identifier}/work-packages` | Create a work package |
| `GET` | `/api/v1/work-packages/{id}` | Get work package details |
| `PUT/PATCH` | `/api/v1/work-packages/{id}` | Update work package attributes |
| `POST` | `/api/v1/work-packages/{id}/status` | Move work package to new status (Kanban drag-and-drop endpoint) |
| `DELETE` | `/api/v1/work-packages/{id}` | Delete work package |

### Time Entries

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/work-packages/{id}/time-entries` | List time entries for a work package |
| `POST` | `/api/v1/work-packages/{id}/time-entries` | Log spent hours |

---

## Example Payload: Move Work Package Status

```http
POST /api/v1/work-packages/42/status
Content-Type: application/json

{
  "status_id": 3,
  "position": 1
}
```

Response:
```json
{
  "success": true,
  "message": "Work package moved to In Review",
  "data": {
    "id": 42,
    "status": {
      "id": 3,
      "name": "In Review",
      "color": "#eab308"
    }
  }
}
```
