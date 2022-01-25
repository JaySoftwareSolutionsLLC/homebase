			<section class="column weather">
			<h2>Weather</h2>
			<div class="content">
			<?php
				$conn = connect_to_db();
				$cities_select = "SELECT * FROM weather_cities";
				$result = $conn->query($cities_select);
				if ($result->num_rows > 0) {
					// TEST PASSED 2018.10.18 echo "CITIES: $result->num_rows<br/>";
			?>
				<script>
					var myCities = [];
					function City(name, lat, lon, active) {
						this.name = name;
						this.lat = lat;
						this.lon = lon;
						this.active = active;
					}
				</script>
			<?php
				while($row = $result->fetch_assoc()) {
						// TEST PASSED 2018.10.18 echo "THIS CITY: " . var_dump($row);
			?>
				<script>
					myCities.push(new City("<?php echo $row["name"]?>", "<?php echo $row["lat"]?>", "<?php echo $row["lon"]?>", "<?php echo $row["active"]?>"));
				</script>
			<?php
					}
				}
			?>
			<script>
				myCities.sort(function(a,b) {
					if (a.name < b.name) {
						return 1;
					} else {
						return -1;
					}
				});
			</script>
			</div>
		</section>