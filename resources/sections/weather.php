			<section class="column weather">
			<h2>Weather</h2>
			<div class="content">
			<?php
				$cities_select = "SELECT * FROM weather_cities";
				$result = $conn->query($cities_select);
				if ($result->num_rows > 0) {
			?>
					<script>
						var myCities = [];
						function City(name, lat, lon) {
							this.name = name;
							this.lat = lat;
							this.lon = lon;
						}
					</script>
			<?php
					while($row = $result->fetch_assoc()) {
			?>
					<script>
						myCities.push(new City("<?php echo $row["name"]?>", "<?php echo $row["lat"]?>", "<?php echo $row["lon"]?>"));
					</script>
			<?php
					}
				}
			?>
			</div>
		</section>