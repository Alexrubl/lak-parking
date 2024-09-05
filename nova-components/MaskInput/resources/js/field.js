import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-mask-input', IndexField)
  app.component('detail-mask-input', DetailField)
  app.component('form-mask-input', FormField)
})
