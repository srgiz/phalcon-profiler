{# profiler/performance #}
{% extends '@profiler/data.volt' %}

{% block panel %}
    <div class="row gx-3">
        <div class="col-auto mb-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div>
                            <div class="card-title fw-medium text-muted">Total execution time</div>
                            <div class="card-text text-light-emphasis fs-5">{{ '%.0F'|format(meta['max']) }}&nbsp;ms</div>
                        </div>
                        <div class="ms-3">
                            <div class="icon-box bg-primary-subtle text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-hourglass-split" viewBox="0 0 16 16">
                                    <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto mb-4">
            <div class="card card-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div>
                            <div class="card-title fw-medium text-muted">Peak memory usage</div>
                            <div class="card-text text-light-emphasis fs-5">{{ '%.2F'|format(_meta['peakMemoryUsage']) }}&nbsp;MiB</div>
                        </div>
                        <div class="ms-3">
                            <div class="icon-box bg-warning-subtle text-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-memory" viewBox="0 0 16 16">
                                    <path d="M1 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.586a1 1 0 0 0 .707-.293l.353-.353a.5.5 0 0 1 .708 0l.353.353a1 1 0 0 0 .707.293H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zm.5 1h3a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5m5 0h3a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 .5-.5m4.5.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5zM2 10v2H1v-2zm2 0v2H3v-2zm2 0v2H5v-2zm3 0v2H8v-2zm2 0v2h-1v-2zm2 0v2h-1v-2zm2 0v2h-1v-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-4">
        <div class="card card-shadow">
            <div class="card-body position-relative" style="height: {{ (data['datasets']|length + 2) * 40 }}px">
                <canvas id="performance"></canvas>
            </div>
        </div>
    </div>
{% endblock %}

{% block js %}
    {% autoescape false %}
        {{ this.profilerAssets.outputInlineFile('@profiler/templates/assets/chart.umd.min.4.4.1.js') }}
        <script>
            const ctx = document.getElementById('performance');

            document.addEventListener('DOMContentLoaded', () => {
                new Chart(ctx, {
                    type: 'bar',
                    data: {{ data|json_encode }},
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        animation: false,
                        elements: {
                            bar: {
                                borderSkipped: false,
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: {
                                max: {{ meta['max'] }},
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#adb5bd'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.labelShort + ': '
                                            + context.dataset.data[context.dataIndex].duration + ' ms / '
                                            + context.dataset.data[context.dataIndex].memory + ' MiB'
                                            ;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    {% endautoescape %}
{% endblock %}
