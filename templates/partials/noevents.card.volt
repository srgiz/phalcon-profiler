{# partials/noevents.card #}
<div class="card card-shadow">
  <div class="card-body">
    {{ title }}

    <div class="mt-2">
      <a data-bs-toggle="collapse" href="#collapseBacktrace" role="button" aria-expanded="false">
        example di
      </a>

<pre class="collapse mt-2 mb-0" id="collapseBacktrace"><code><span class="text-secondary"># services.yaml</span>
<span class="text-code">{{ service }}</span>:
  <span class="text-code">calls</span>:
    - <span class="text-code">method</span>: setEventsManager
      <span class="text-code">arguments</span>:
        - { <span class="text-code">type</span>: service, <span class="text-code">name</span>: eventsManager }</code></pre>

    </div>
  </div>
</div>
