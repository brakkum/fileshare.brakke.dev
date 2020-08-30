

// share page
const copy_link = document.getElementById("copy-link");

copy_link.onclick = () => {
    const link_url = document.getElementById("file-link");
    link_url.select();
    link_url.setSelectionRange(0, 99999);
    document.execCommand("copy");
    link_url.classList.add("is-success");
}
// end share page
