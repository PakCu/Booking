// Custom JavaScript for SPD Production

$(document).ready(function() {
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });

    // Auto-hide alerts after 5 seconds
    $('.alert').not('.alert-warning').delay(5000).fadeOut('slow');
});