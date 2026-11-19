(function () {
  const data = window.dashboardData;
  if (!data || typeof Chart === "undefined") return;

  const chartColors = {
    primary: "#8A5F41",
    secondary: "#A77F60",
    accent: "#CCD67F",
    success: "#7A8F4A",
    danger: "#B45B4A",
    lightAccent: "#EDE0C8"
  };

  const barColors = [
    chartColors.primary,
    chartColors.secondary,
    chartColors.accent,
    chartColors.success,
    chartColors.danger
  ];

  function money(value) {
    return "$" + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderGeneralOverviewChart() {
    const ctx = document.getElementById("generalOverviewChart").getContext("2d");

    new Chart(ctx, {
      type: "line",
      data: {
        labels: data.overview.map(function (p) { return p.label; }),
        datasets: [
          {
            label: "Stock Value ($)",
            data: data.overview.map(function (p) { return p.value; }),
            borderColor: chartColors.primary,
            backgroundColor: chartColors.primary,
            yAxisID: "y",
            tension: 0.3
          },
          {
            label: "Quantity in Stock",
            data: data.overview.map(function (p) { return p.quantity; }),
            borderColor: chartColors.success,
            backgroundColor: chartColors.success,
            yAxisID: "y1",
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        plugins: {
          legend: { position: "bottom" },
          tooltip: {
            callbacks: {
              label: function (item) {
                if (item.dataset.yAxisID === "y") {
                  return item.dataset.label.replace(" ($)", "") + ": " + money(item.parsed.y);
                }
                return item.dataset.label + ": " + item.parsed.y;
              },
              afterBody: function (items) {
                return data.overview[items[0].dataIndex].detail;
              }
            }
          }
        },
        scales: {
          y: {
            type: "linear",
            position: "left",
            beginAtZero: true,
            title: { display: true, text: "Stock Value ($)" }
          },
          y1: {
            type: "linear",
            position: "right",
            beginAtZero: true,
            title: { display: true, text: "Quantity" },
            grid: { drawOnChartArea: false }
          }
        }
      }
    });
  }

  function renderCategoryChart() {
    const ctx = document.getElementById("categoryChart").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.categories.map(function (c) { return c.label; }),
        datasets: [{
          label: "Inventory Value",
          data: data.categories.map(function (c) { return c.value; }),
          backgroundColor: data.categories.map(function (c, i) { return barColors[i % barColors.length]; })
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (item) { return money(item.parsed.y); }
            }
          }
        },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  function renderStatusChart() {
    const ctx = document.getElementById("statusChart").getContext("2d");

    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: ["In Stock", "Low Stock", "Out of Stock"],
        datasets: [{
          data: [data.status.inStock, data.status.lowStock, data.status.outOfStock],
          backgroundColor: [chartColors.success, chartColors.accent, chartColors.danger]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: "bottom" } }
      }
    });
  }

  function renderTopProductsChart() {
    const ctx = document.getElementById("topProductsChart").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.top.map(function (p) { return p.label; }),
        datasets: [{
          label: "Stock Value",
          data: data.top.map(function (p) { return p.value; }),
          backgroundColor: chartColors.primary
        }]
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (item) { return money(item.parsed.x); }
            }
          }
        },
        scales: { x: { beginAtZero: true } }
      }
    });
  }

  renderGeneralOverviewChart();
  renderCategoryChart();
  renderStatusChart();
  renderTopProductsChart();
})();
