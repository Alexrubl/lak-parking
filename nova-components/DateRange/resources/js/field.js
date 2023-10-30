import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'


Nova.booting((app, store) => {
  app.component('index-date-range', IndexField)
  app.component('detail-date-range', DetailField)
  app.component('form-date-range', FormField)
})
