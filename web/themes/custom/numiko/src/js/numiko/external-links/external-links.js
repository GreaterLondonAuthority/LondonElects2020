  
/*
 * Open all external links in new window. Add title, class and rel=noopener.
 */
const links = document.links;

for(var i=0; i<links.length; i++) {
  const link = links[i];
  if (!link.classList.contains('js-skip-external-link') && 
      location.hostname !== link.hostname && 
      link.hostname.slice(0, 1) !== '/' &&
      link.hostname.length) {
    link.setAttribute('target', '_blank');
    link.setAttribute('rel', 'noopener');
    link.setAttribute('title', 'Opens in new window');
    link.className += " external-link";
  }
}