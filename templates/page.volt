{# page #}
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
{{ partial('@profiler/partials/head') }}
<body>
{{ partial('@profiler/partials/header.nav') }}
<div class="container gx-4">
    {% block content %}{% endblock %}
</div>
{{ partial('@profiler/partials/footer') }}
</body>
</html>
