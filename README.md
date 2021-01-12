### Swagger decorator for API Platform, adding JWT endpoints

Install the package:

`composer req colvin/api-platform-swagger-decorator`

Define the service in `services.yaml`:

```
services:
    Colvin\Swagger\Decorator:
        decorates: 'api_platform.openapi.factory'
        autoconfigure: false
```

If you go to your api docs page you should now see a new group with JWT authentication and refresh endpoints.