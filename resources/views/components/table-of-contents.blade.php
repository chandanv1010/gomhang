<div class="table-of-contents border rounded-md p-3 shadow-sm bg-white">
    <div class="uk-flex uk-flex-space-between uk-flex-middle" style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ccc;">
        <h3 class="font-bold m-0" style="margin:0;">Mục lục</h3>
        <button id="toggle-toc" class="text-blue-600 text-sm hover:underline" style="background: transparent;border:0;">Ẩn</button>
    </div>

    <ul id="toc-list" class="toc-list mt-2">
        @foreach ($items as $item)
            <li class="level-{{ $item['level'] }} mb-1">
                <a href="#{{ $item['id'] }}" class="toc-link">
                    {{ $item['numbering'] }}. {{ $item['text'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById("toggle-toc");
        const list = document.getElementById("toc-list");

        btn.addEventListener("click", function() {
            if (list.style.display === "none") {
                list.style.display = "block";
                btn.textContent = "Ẩn";
            } else {
                list.style.display = "none";
                btn.textContent = "Hiện";
            }
        });

        // Smooth scroll (jQuery)
        $('a.toc-link').on('click', function(e) {
            e.preventDefault();
            let target = $(this).attr('href');
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        });
    });
</script>
