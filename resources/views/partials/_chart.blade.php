<div class="chart">
	<div class="chart-cont" id="{{$chartId}}" data-chart-size-y="{{$chartYsize}}" data-chart-size-x="{{$chartXsize}}">
		<div class="x-labels-grad"></div>
		<div class="chart-axis__x-labels f2_6">
			@for ($i=10; $i > 0; $i--)
				<span style="top: -{{$i*10}}%">${{number_format($first_price+$i*$chartYsize, 2, '.', '')}}</span>
			@endfor
			<span style="top: 0%">${{number_format($first_price, 2, '.', '')}}</span>
			@for ($i=1; $i < 10; $i++)
				<span style="top: {{$i*10}}%">${{number_format($first_price-$i*$chartYsize, 2, '.', '')}}</span>
			@endfor
		</div>


		<div class="chart-right">
			<div class="chart-inner">
				<div class="chart-inner-error">
					<div class="error-midBox">
						<span class="f1_8" id="error-msg">Sorry something went wrong...</span>
						<button type="button" id="error-reload" class="btn__0-light">Reload</button>
					</div>
					<div class="errorBck">

					</div>
				</div>

				<svg class="chart-axis__y chart-axY chart-a__y-rounds" preserveAspectRatio="xMinYMin meet" viewBox="0 0 10000 1000">
					<g stroke-dasharray="180" fill="none" transform="translate(-5, 0)">
					</g>
				</svg>

				<svg viewBox="0 0 10000 1000" class="chart-content chart-axY" preserveAspectRatio="xMinYMin meet" data-axis_y0="{{$first_price}}">
					<defs>
						<linearGradient id="graph-grad" gradientUnits="userSpaceOnUse" clip-path="inset(0 0 0 0)">
							<stop  offset="0" style="stop-color:#076ddd"/>
							<stop  offset="1" style="stop-color:#4c8afa"/>
						</linearGradient>

			   	</defs>

					<g transform="translate(-5, 3000)" chart-content chart-cont-before="{{$prices_usd}}" chart-last-x="0">
						<line x1="-10000" y1="3000" x2="20000" y2="3000" class="gameLine-bet__price" id="gameLine-currPrice"></line>

						<path class="chart-upstroke" chart-stroke stroke="url(#graph-grad)" mask="url(#fade)">
						</path>

						<path d="M0,10000000L0,10000000" class="chart-fill_area" chart-area  fill="url(#graph-grad)">
						</path>
					</g>
				</svg>

				<svg class="chart-axis__x" preserveAspectRatio="xMinYMin meet" viewBox="0 0 10000 1000">
					<g stroke-dasharray="180" fill="none" transform="translate(-0, 2993)">
						@for ($i=10000; $i > 0; $i-=1000)
							<line x1="0" y1="-{{$i}}" x2="10000" y2="-{{$i}}" transform="translate(0, 0)"></line>
						@endfor

						@for ($i=0; $i <= 10000; $i+=1000)
							<line x1="0" y1="{{$i}}" x2="10000" y2="{{$i}}" transform="translate(0, 0)"></line>
						@endfor
					</g>
				</svg>


				<svg class="chart-axis__y chart-axY chart-a__y-lines" preserveAspectRatio="xMinYMin meet" viewBox="0 0 10000 1000">
					<g stroke-dasharray="180" fill="none" transform="translate(-5, 0)">
						@for ($i=1000; $i <= 10000; $i+=1000)
							<line x1="{{$i}}" y1="0" x2="{{$i}}" y2="10000" transform="translate(0, 0)"></line>
						@endfor
					</g>
				</svg>
			</div>


			<div class="chart-axis__y-labels chart-axY f2_6" transform="translate(-5, 0)">
				<div class="y-labels-grad"></div>
				@for ($i=0; $i < 30; $i++)
					@php
						$newChartTime = Carbon\Carbon::parse($first_time)->addSecond($i*$chartXsize);
						$newTimeFormat = $newChartTime->format('H:i:s');

						$geoIpData = geoip(session('user_ip'));
						$newTimeTIMEZONE = $newChartTime->setTimezone($geoIpData->timezone)->format('H:i:s');
					@endphp
					<span style="left: {{$i*10}}%" data-time="{{$newTimeFormat}}">{{$newTimeTIMEZONE}}</span>
				@endfor

			</div>
		</div>

	</div>
</div>
