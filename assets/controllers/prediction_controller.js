import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'prediction' ];

    static values = {
        url: String
    };

    static outlets = ['notification']

    async savePrediction() {
        let data = [];

        this.predictionTargets.forEach((row, index) => {
            const match = row.querySelector('.matchId') ? row.querySelector('.matchId').value : '';
            const competition = row.querySelector('.competitionId') ? row.querySelector('.competitionId').value : '';
            const homeTeamScore = row.querySelector('.homeTeamScore') ? row.querySelector('.homeTeamScore').value : '';
            const awayTeamScore = row.querySelector('.awayTeamScore') ? row.querySelector('.awayTeamScore').value : '';
            const startTime = row.querySelector('.startTime').textContent;

            if (homeTeamScore !== '' || awayTeamScore !== '') {
                if (!isNaN(homeTeamScore) || !isNaN(awayTeamScore) || !isNaN(competition) || !isNaN(match)) {
                    data.push({match, competition, startTime, homeTeamScore, awayTeamScore});
                }
            }
        });

        let predictions = new FormData();
        predictions.append('data', JSON.stringify(data));

        const response = await fetch(this.urlValue, {
            method: 'POST',
            body: new URLSearchParams(predictions)
        });

        this.notificationOutlet.showNotification(await response.text());
    }
}