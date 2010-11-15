/**
 * Generate weather entries based on location
 * 
 * The JSON request is build up like this:
 * 
 * data -> current_condition [weather_data]
 * 		-> request [request data]
 * 		-> weather [day 1 weather data]
 * 		-> weather [day n weather data]
 *
 * SAMPLE: 
 * 
 * { "data": {
 * "current_condition": [ {"cloudcover": "4", "humidity": "73", "observation_time": "08:49 PM", "precipMM": "0.5", "pressure": "1023", "temp_C": "19", "temp_F": "66", "visibility": "10", "weatherCode": "113",  "weatherDesc": [ {"value": "Clear" } ],  "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0008_clear_sky_night.png" } ], "winddir16Point": "ENE", "winddirDegree": "70", "windspeedKmph": "9", "windspeedMiles": "6" } ],  
 * "request": [ {"query": "Lat 48.83 and Lon 2.39", "type": "LatLon" } ],
 * "weather": [
 * {"date": "2010-11-03", "precipMM": "0.4", "tempMaxC": "16", "tempMaxF": "60", "tempMinC": "10", "tempMinF": "51", "weatherCode": "116", "weatherDesc": [ {"value": "Partly Cloudy" } ], "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0002_sunny_intervals.png" } ], "winddir16Point": "SW", "winddirDegree": "231", "winddirection": "SW", "windspeedKmph": "18", "windspeedMiles": "11" },
 * {"date": "2010-11-04", "precipMM": "0.6", "tempMaxC": "17", "tempMaxF": "63", "tempMinC": "13", "tempMinF": "55", "weatherCode": "119", "weatherDesc": [ {"value": "Cloudy" } ], "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0003_white_cloud.png" } ], "winddir16Point": "SW", "winddirDegree": "235", "winddirection": "SW", "windspeedKmph": "18", "windspeedMiles": "11" },
 * {"date": "2010-11-05", "precipMM": "0.2", "tempMaxC": "14", "tempMaxF": "57", "tempMinC": "10", "tempMinF": "50", "weatherCode": "116", "weatherDesc": [ {"value": "Partly Cloudy" } ], "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0002_sunny_intervals.png" } ], "winddir16Point": "SW", "winddirDegree": "229", "winddirection": "SW", "windspeedKmph": "18", "windspeedMiles": "11" },
 * {"date": "2010-11-06", "precipMM": "3.7", "tempMaxC": "13", "tempMaxF": "56", "tempMinC": "9", "tempMinF": "48", "weatherCode": "119", "weatherDesc": [ {"value": "Cloudy" } ], "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0003_white_cloud.png" } ], "winddir16Point": "SSW", "winddirDegree": "212", "winddirection": "SSW", "windspeedKmph": "26", "windspeedMiles": "16" }, 
 * {"date": "2010-11-07", "precipMM": "5.4", "tempMaxC": "8", "tempMaxF": "47", "tempMinC": "4", "tempMinF": "38", "weatherCode": "296", "weatherDesc": [ {"value": "Light rain" } ], "weatherIconUrl": [ {"value": "http:\/\/www.worldweatheronline.com\/images\/wsymbols01_png_64\/wsymbol_0017_cloudy_with_light_rain.png" } ], "winddir16Point": "NE", "winddirDegree": "39", "winddirection": "NE", "windspeedKmph": "16", "windspeedMiles": "10" }
 * ] }}
 */
function getWeather(selectedLocation) {
	listOfWeather = [];
	jQuery(function() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = "http://www.worldweatheronline.com/feed/weather.ashx?format=json&key=f7d8c40a98131239100311&q="
				+ selectedLocation.lat()
				+ ","
				+ selectedLocation.lng()
				+ "&num_of_days=4"
				+ "&callback=processResults";
		$("body").append(script);
	});
}

function processResults(jsonData) {
	var shtml = '';
	var description = "";
	var icon = "";
	var weather = jsonData.data.weather;
	var current = jsonData.data.current_condition;
	var i = 0;
	var output = "";

	if (current) {
		description = current[0].weatherDesc[0].value;
		icon = "images/weather/" + description + ".png";
		output =  "<td class='weather-text'>" + current[0].observation_time + "<br/>";
		output += "<img class='weather-icon' alt='" + description + "' title='" + description + "' src='" + icon + "'/>";
		output += "</td>";
		output += "<td class='weather-text'>";
		output += "<span class='weather-text-max-temp'>" + current[0].temp_C + "C</span><br/>";
		output += "<img title='Wind direction' src='images/weather/wind-direction/" + current[0].winddir16Point + ".gif' class='wind-direction-icon'/> " + current[0].windspeedKmph + "km/h<br/>";
		output += "<img title='Precipitation' src='images/weather/precipitation.jpg' class='precipitation-icon'/>&nbsp;&nbsp;" + current[0].precipMM + "mm<br/>";
		output += "</td>";
	}

	listOfWeather[0] = output;
	
	if (weather) {
		for (i = 1; i < 3; i++) {	
			if (weather[i]) {
				description = weather[i].weatherDesc[0].value;
				icon = "images/weather/" + description + ".png";
				output =  "<td width='50px' class='weather-text'>" + getDayOfWeek(weather[i].date) + "<br/>";
				output += "<img class='weather-icon' alt='" + description + "' title='" + description + "' src='" + icon + "'/>";
				output += "</td>";
				output += "<td class='weather-text'>";
				output += "<span class='weather-text-min-temp'>" + weather[i].tempMinC + "C</span>" + " / " + "<span class='weather-text-max-temp'>" + weather[i].tempMaxC + "C</span><br/>";
				output += "<img title='Wind direction' src='images/weather/wind-direction/" + weather[i].winddirection + ".gif' class='wind-direction-icon'/> " + weather[i].windspeedKmph + "km/h<br/>";
				output += "<img title='Precipitation' src='images/weather/precipitation.jpg' class='precipitation-icon'/>&nbsp;&nbsp;" + weather[i].precipMM + "mm<br/>";
				output += "</td>";
				listOfWeather[i] = output;
			}
		}
	}

	if (listOfWeather[0] == "") {
		listOfWeather[0] = "There was a problem loading weather data, the service may be down.";
	}
	updateGeneralDisplay();
}

function getDayOfWeek(date) {
	var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
	var myDate=new Date(eval('"' + date + '"'));
	return days[myDate.getDay()];
}