import { getHighlighter } from 'shiki'

export default function highlighter(options) {

  if (! options) {
    options = {
      lang: 'json',
      theme: 'material-theme-palenight'
    }
  }

  return {
    async init() {
      const highlighter = await getHighlighter({
        themes: ['material-theme-palenight'],
        langs: ['json', 'javascript', 'php', 'css'],
      })

      this.$refs.code.innerHTML = highlighter.codeToHtml(this.$refs.code.innerHTML, options)
    }
  }
}