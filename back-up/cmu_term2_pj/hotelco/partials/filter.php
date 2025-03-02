<div class="card shadow-sm p-3 sticky-top" style="top: 80px;">
    <h5 class="mb-3"><i class="bi bi-funnel-fill"></i> Filters</h5>
    <form method="GET" action="">
        <!-- Country Filter -->
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <select name="country" id="country" class="form-select">
                <option value="">All Countries</option>
                <?php
                // Get the selected value and trim it
                $selectedCountry = isset($_GET['country']) ? trim($_GET['country']) : '';
                $countries = $pdo->query("SELECT DISTINCT country FROM hotels ORDER BY country")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($countries as $country) {
                    // Trim the country value from the database
                    $countryTrim = trim($country);
                    $isSelected = ($selectedCountry === $countryTrim) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($countryTrim) . "' $isSelected>" . htmlspecialchars($countryTrim) . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Province Filter -->
        <div class="mb-3">
            <label for="province" class="form-label">Province</label>
            <select name="province" id="province" class="form-select">
                <option value="">All Provinces</option>
                <?php
                $selectedProvince = isset($_GET['province']) ? trim($_GET['province']) : '';
                $provinces = $pdo->query("SELECT DISTINCT province FROM hotels ORDER BY province")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($provinces as $province) {
                    $provinceTrim = trim($province);
                    $isSelected = ($selectedProvince === $provinceTrim) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($provinceTrim) . "' $isSelected>" . htmlspecialchars($provinceTrim) . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Min Price -->
        <div class="mb-3">
            <label for="min_price" class="form-label">Min Price</label>
            <input type="number" name="min_price" id="min_price" class="form-control" 
                   value="<?php echo isset($_GET['min_price']) ? htmlspecialchars(trim($_GET['min_price'])) : ''; ?>" placeholder="Min Price">
        </div>

        <!-- Max Price -->
        <div class="mb-3">
            <label for="max_price" class="form-label">Max Price</label>
            <input type="number" name="max_price" id="max_price" class="form-control" 
                   value="<?php echo isset($_GET['max_price']) ? htmlspecialchars(trim($_GET['max_price'])) : ''; ?>" placeholder="Max Price">
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Apply Filters
        </button>
    </form>
</div>