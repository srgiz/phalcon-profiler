{# base #}
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
{{ partial('@profiler/partials/head') }}
<body>
{{ partial('@profiler/partials/header.nav') }}
<div class="profiler container-xxl">
    {% block content %}{% endblock %}
</div>
{{ partial('@profiler/partials/footer') }}
</body>
</html>
