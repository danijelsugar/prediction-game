$(document).ready(function() {
    $('#savePrediction').on('click', function (e) {
        let data = [];
        let match = $('.matchId');
        match.each(function() {
            
            let homeTeamScore = $(this).closest('tr.match').find('.homeTeamScore').val();
            let awayTeamScore = $(this).closest('tr.match').find('.awayTeamScore').val();
            let startTime = $(this).closest('tr.match').find('.startTime').text();
            let competition = $(this).closest('tr.match').find('.competitionId').val();
            let matchId = $(this).val();

            if ('' === homeTeamScore || '' === awayTeamScore) {
                return true;
            }

            if (!$.isNumeric(homeTeamScore) || !$.isNumeric(awayTeamScore) || !$.isNumeric(competition) || !$.isNumeric(matchId)) {
                return true;
            }
            
            data.push({'match': matchId, 'competition': competition, 'startTime': startTime, 'homeTeam': homeTeamScore, 'awayTeam': awayTeamScore});
            
        })

        $.ajax({
            url: $('.table').attr('data-url'),
            type: 'POST',
            dataType: 'json',
            data: {data: JSON.stringify(data)},
            async: true,
            success: function (response) {
                $('.prediction-notification div.alert-success .prediction-notification-message').text('')
                $('.prediction-notification div.alert-success .prediction-notification-message').append(document.createTextNode(response));

                if ($('.prediction-notification div.alert-success').hasClass('visuallyhidden')) {
                    $('.prediction-notification div.alert-success').removeClass('visuallyhidden');
                }

                setTimeout(function () {
                    if (!$('.prediction-notification div.alert-success').hasClass('visuallyhidden')) {
                        $('.prediction-notification div.alert-success').addClass('visuallyhidden');
                    }
                }, 5000)
            },
            error: function (xhr, status, error) {
                $('.prediction-notification div.alert-danger .prediction-notification-message').text('');
                $('.prediction-notification div.alert-danger .prediction-notification-message').append(document.createTextNode(xhr.responseJSON));

                if ($('.prediction-notification div.alert-danger').hasClass('visuallyhidden')) {
                    $('.prediction-notification div.alert-danger').removeClass('visuallyhidden');
                }

                setTimeout(function () {
                    if (!$('.prediction-notification div.alert-danger').hasClass('visuallyhidden')) {
                        $('.prediction-notification div.alert-danger').addClass('visuallyhidden');
                    }
                }, 5000)
            }
        });
    });

    $('.close-notification').on('click', function () {
        const alert = this.closest('.alert');
        if (!$(alert).hasClass('visuallyhidden')) {
            $(this.closest('.alert')).addClass('visuallyhidden')
        }
    })
})