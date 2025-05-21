Echo.channel(`display.${colla_shortname}`)
    .listen('PublicDisplayUpdated', (e) => {
        window.location.reload(false);
    });
