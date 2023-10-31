(()=>{
  const customCss = `
  #title-heading {clear:left}
  `;

  require('https://cdn.jsdelivr.net/npm/jszip@3.2.2/dist/jszip.min.js')
  .then(ret => {
    const zip = new JSZip;

    const css = [];
    for(let n of document.querySelectorAll('link[rel="stylesheet"][href^="/"]')) {
      css.push(new Promise((resolve) => {
        fetch(n.href).then(r => resolve(r.text()));
      }))
    }

    Promise.all(css)
    .then(css => css.join('\n'))
    .then(css => css.replace(/\/\*.*?\*\//gs, ''))
    .then(css => {
      const paths = new Map;
      const pattern = /\burl\((['"])(?<url>\/.*?)(?<hash>#.*?)?\1\)/g;
      const rewritten = css.replace(pattern, (str, quote, url, hash = '') => {
        const name = url.split('?', 2).shift().split('/').pop();
        if(!paths.has(url)) paths.set(url, `./assets/file-${paths.size}-${name}`);
        return `url("${paths.get(url)}${hash}")`;
      })
      return {css: rewritten, paths};
    })
    .then(({css, paths}) => {
      zip.file('site.css', new Blob([css + customCss]));
      console.log('Fetching files ...');
      const p = Array.from(paths).map(([url, local]) => fetch(url)
        .then(r => r.blob())
        .then(blob => zip.file(local, blob)));

      return Promise.all(p);
    })
    .then(() => {
      console.log('Generating archive ...');
      return zip.generateAsync({type: 'blob'});
    })
    .then(blob => {
      const a = document.createElement('a');
      a.download = 'styles.zip';
      a.href = URL.createObjectURL(blob);
      a.click();
    });

  });

  function require(url) {
    return fetch(url)
      .then(res => res.text())
      .then(src => (new Function('var exports={};'+src+';return exports')).call({}).exports);
  }
})()