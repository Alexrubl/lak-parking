import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-time-range', IndexField)
  app.component('detail-time-range', DetailField)
  app.component('form-time-range', FormField)
})
