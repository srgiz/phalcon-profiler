<body>

{{ partial('nav') }}

<h4>Content</h4>

{% block content %}{% endblock %}

{% if _profilerTag is defined %}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ url(['for': '_profiler-bar', 'tag': _profilerTag])|escape_js }}')
                .then(function(res) { return res.text() })
                .then(function(data) {
                    document.body.innerHTML += data
                })
                .catch(function(e) {
                    console.error(e)
                })
        })
    </script>
{% endif %}

</body>
