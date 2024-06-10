import Card from './components/Card'

Nova.booting((app, store, router) => {
    app.component('reload-resources', Card)
})
