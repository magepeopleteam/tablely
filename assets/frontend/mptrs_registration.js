jQuery(document).ready(function($) {
    $(document).on( "click",".mptrs_faq_question",function() {
        let parent = $(this).closest(".mptrs_faq_item");

        // Collapse other open items
        $(".mptrs_faq_item").not(parent).removeClass("faqActive")
            .find(".mptrs_faq_answer").slideUp(200);
        $(".mptrs_faq_item").not(parent)
            .find(".mptrs_faq_icon").text("+");

        // Toggle current
        parent.toggleClass("faqActive");
        parent.find(".mptrs_faq_answer").slideToggle(200);
        let icon = parent.find(".mptrs_faq_icon");
        icon.text(icon.text() === "+" ? "−" : "+");
    });
});