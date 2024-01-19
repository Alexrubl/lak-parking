import DateRangeFilter from './components/DateRangeFilter'

Nova.booting((app, store) => {
  app.component('daterange-filter', DateRangeFilter)
})
