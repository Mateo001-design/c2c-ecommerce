/**
 * iTradeZA C2C E-Commerce Platform – Main JavaScript
 */

$(document).ready(function () {

    // ── Confirm Delete Actions ──────────────────────────────
    $('form[data-confirm]').on('submit', function (e) {
        if (!confirm($(this).data('confirm') || 'Are you sure?')) {
            e.preventDefault();
        }
    });

    // ── Auto-dismiss Alerts ─────────────────────────────────
    setTimeout(function () {
        $('.alert-dismissible').fadeOut(500);
    }, 4000);

    // ── Image Preview on File Input ─────────────────────────
    $('input[type="file"][name="images[]"], input[type="file"][name="profile_image"]').on('change', function () {
        var preview = $(this).closest('.mb-3').find('.img-preview');
        if (!preview.length) {
            preview = $('<div class="img-preview d-flex gap-2 mt-2 flex-wrap"></div>');
            $(this).closest('.mb-3').append(preview);
        }
        preview.empty();

        var files = this.files;
        for (var i = 0; i < Math.min(files.length, 5); i++) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.append(
                    '<img src="' + e.target.result + '" class="img-thumbnail" style="width:80px;height:80px;object-fit:cover;">'
                );
            };
            reader.readAsDataURL(files[i]);
        }
    });

    // ── Search Auto-submit on Category/Sort Change ──────────
    $('select[name="cat"], select[name="sort"], select[name="condition"]').on('change', function () {
        $(this).closest('form').submit();
    });

    // ── Cart Quantity Validation ─────────────────────────────
    $('input[name="quantity"]').on('change', function () {
        var val = parseInt($(this).val());
        if (isNaN(val) || val < 1) $(this).val(1);
    });

    // ── Back to Top Button ──────────────────────────────────
    var backToTop = $('<button class="btn btn-success btn-sm position-fixed" style="bottom:20px;right:20px;display:none;z-index:9999;border-radius:50%;width:40px;height:40px;"><i class="bi bi-arrow-up"></i></button>');
    $('body').append(backToTop);

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 300) {
            backToTop.fadeIn();
        } else {
            backToTop.fadeOut();
        }
    });

    backToTop.on('click', function () {
        $('html, body').animate({ scrollTop: 0 }, 400);
    });

    // ── Form Validation Feedback ────────────────────────────
    $('form').on('submit', function () {
        var valid = true;
        $(this).find('[required]').each(function () {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return valid;
    });

    // ── Tooltip Init ────────────────────────────────────────
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

});
