import PhotoSwipeLightbox from 'photoswipe/lightbox';

function initLightbox() {
    const lightbox = new PhotoSwipeLightbox({
        gallery: '#my-gallery',
        children: 'a',
        pswpModule: () => import('photoswipe')
    });
    lightbox.init();
}

document.addEventListener('DOMContentLoaded', initLightbox);
document.addEventListener('livewire:update', initLightbox); // Filament refresh
document.addEventListener('livewire:navigated', initLightbox); // Filament navigation
