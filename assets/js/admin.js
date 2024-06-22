document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".nav-tab");
    const contents = document.querySelectorAll(".tab-content");

    function openTab(tabName) {
        contents.forEach(content => {
            content.classList.remove("active");
        });
        tabs.forEach(tab => {
            tab.classList.remove("nav-tab-active");
        });
        document.getElementById("content-" + tabName).classList.add("active");
        document.getElementById("tab-" + tabName).classList.add("nav-tab-active");
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            const tabName = this.id.replace("tab-", "");
            openTab(tabName);
        });
    });

    openTab('general');
});