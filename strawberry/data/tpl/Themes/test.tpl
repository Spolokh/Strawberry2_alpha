<ul class="mainContainer">
{{ BEGIN row }}
    <h2>{{ category }}</h2>
    <li>
        <figure>
            <img src="{{ image }}" loading="lazy" alt="" />
            <a title="{{ title }}" class="caption" href="{{ link }}">
                <b>{{ title }}</b>
                <div class="story">
                    <div class="views"><i class="icon-eye-open"></i> &nbsp; {{ views }}</div>
                    <div class="comms"><i class="icon-comment-alt"></i> &nbsp; {{ comms }}</div>
                    <br clear="both" />
                    {{ short }}
                </div>
            </a>
        </figure>
    </li>
{{ END }}
</ul>
