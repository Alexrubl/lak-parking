import Card from './components/Card'
import './echo';

Nova.booting((app, store) => {
    app.component('video', Card)
})
