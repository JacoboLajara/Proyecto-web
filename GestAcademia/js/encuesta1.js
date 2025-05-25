document.addEventListener('DOMContentLoaded', function() {
    // Pre-fill date
     const today = new Date();
    const day = String(today.getDate()).padStart(2, '0');
    const month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    const year = today.getFullYear();
    const responseDateInput = document.getElementById('responseDate');
    if (!responseDateInput.value) { // Only prefill if empty
        responseDateInput.value = `${day}/${month}/${year}`;
    }

    const form = document.getElementById('satisfactionForm');
    const reportOutputDiv = document.getElementById('reportOutput');
    const reportContentPre = document.getElementById('reportContent');
    const chartCanvas = document.getElementById('scoreChart');
    let myScoreChart = null; // To store the chart instance

    // Score mapping
    const scoreMap = {
        "Excelente": 10,
        "Bueno": 7,
        "Correcto": 5,
        "Regular": 3,
        "Deficiente": 1
    };

    function getScore(value) {
        return scoreMap[value] || 0; // Return 0 if value not in map (e.g., not answered)
    }

    // Add/remove class for selected radio label styling
    const radioLabels = document.querySelectorAll('.likert-scale label');
    radioLabels.forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', () => {
                const groupName = radio.name;
                document.querySelectorAll(`input[name="${groupName}"]`).forEach(rb => {
                    rb.closest('label').classList.remove('selected-label');
                });
                if (radio.checked) {
                    label.classList.add('selected-label');
                }
            });
        }
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        let report = "INFORME DE EVALUACIÓN DE SATISFACCIÓN\n";
        report += "=======================================\n\n";
        report += `Curso: ${formData.get('courseName')}\n`;
        report += `Fecha de Respuesta: ${formData.get('responseDate')}\n\n`;

        // --- Initialize scores for blocks ---
        const blockScores = {
            "Valoración Global Curso SL": { score: 0, maxScore: 0, questions: [] },
            "Valoración Conceptual Curso SL": { score: 0, maxScore: 0, questions: [] },
            "Personal e Instalaciones SL": { score: 0, maxScore: 0, questions: [] },
            "Satisfacción vs Competidores SL": { score: 0, maxScore: 0, questions: [] },
            "Valoración Profesorado": { score: 0, maxScore: 0, questions: [] }
        };
        
        const maxPointsPerQuestion = 10;

        // --- Process SELECT LOGICAL questions ---
        report += "--- EVALUACIÓN DE SELECT LOGICAL, S. L. ---\n";
        
        // Q1
        let q1_val = formData.get('q1_valoracion_global');
        let q1_score = getScore(q1_val);
        blockScores["Valoración Global Curso SL"].score += q1_score;
        blockScores["Valoración Global Curso SL"].maxScore += maxPointsPerQuestion;
        blockScores["Valoración Global Curso SL"].questions.push(`1. Valoración global del curso: ${q1_val || 'No respondido'} (${q1_score} pts)`);

        // Q2
        const q2_items = [
            { name: 'q2_1_expectativas', text: 'Expectativas cubiertas' },
            { name: 'q2_2_material', text: 'Material adecuado' },
            { name: 'q2_3_conocimientos', text: 'Conocimientos ampliados' },
            { name: 'q2_4_equipos', text: 'Equipos prácticos' },
            { name: 'q2_5_medios', text: 'Variedad de medios y ejercicios' }
        ];
        q2_items.forEach(item => {
            let val = formData.get(item.name);
            let score = getScore(val);
            blockScores["Valoración Conceptual Curso SL"].score += score;
            blockScores["Valoración Conceptual Curso SL"].maxScore += maxPointsPerQuestion;
            blockScores["Valoración Conceptual Curso SL"].questions.push(`   - ${item.text}: ${val || 'No respondido'} (${score} pts)`);
        });

        // Q3
        const q3_items = [
            { name: 'q3_1_info_doc', text: 'Información y documentación práctica' },
            { name: 'q3_2_personal_servicio', text: 'Personal da buen servicio' },
            { name: 'q3_3_instalaciones', text: 'Instalaciones adecuadas' }
        ];
         q3_items.forEach(item => {
            let val = formData.get(item.name);
            let score = getScore(val);
            blockScores["Personal e Instalaciones SL"].score += score;
            blockScores["Personal e Instalaciones SL"].maxScore += maxPointsPerQuestion;
            blockScores["Personal e Instalaciones SL"].questions.push(`   - ${item.text}: ${val || 'No respondido'} (${score} pts)`);
        });

        // Q4
        let q4_val = formData.get('q4_competidores');
        let q4_score = getScore(q4_val);
        blockScores["Satisfacción vs Competidores SL"].score += q4_score;
        blockScores["Satisfacción vs Competidores SL"].maxScore += maxPointsPerQuestion;
        blockScores["Satisfacción vs Competidores SL"].questions.push(`4. Satisfacción vs. competidores: ${q4_val || 'No respondido'} (${q4_score} pts)`);


        // --- Append detailed scores to report ---
        Object.values(blockScores).forEach(block => {
            if (block.questions.length > 0 && block.questions[0].startsWith('1.')) { // For main questions like Q1
                 report += block.questions.join('\n') + '\n';
            } else if (block.questions.length > 0 && block.questions[0].startsWith('4.')) { // for Q4
                 report += block.questions.join('\n') + '\n';
            }
        });
        
        // Specific handling for multi-item blocks to have a header in the report
        if (blockScores["Valoración Conceptual Curso SL"].questions.length > 0) {
            report += "2. Nivel de valoración conceptual del curso:\n";
            report += blockScores["Valoración Conceptual Curso SL"].questions.join('\n') + '\n';
        }
        if (blockScores["Personal e Instalaciones SL"].questions.length > 0) {
            report += "3. Nivel de valoración de Personal e Instalaciones:\n";
            report += blockScores["Personal e Instalaciones SL"].questions.join('\n') + '\n';
        }


        report += `5. Aspectos a mejorar (SELECT LOGICAL): \n   ${formData.get('q5_mejorar_select') || 'Sin comentarios'}\n`;
        report += `6. Sugerencias mejora continua (SELECT LOGICAL): \n   ${formData.get('q6_sugerencias_select') || 'Sin comentarios'}\n\n`;


        // --- Process PROFESORADO questions ---
        report += "--- EVALUACIÓN DEL PROFESORADO ---\n";
        report += `Profesor: ${formData.get('profesorName')}\n`;
        
        const pq1_items = [
            { name: 'pq1_1_claridad', text: 'Exponen con claridad' },
            { name: 'pq1_2_atienen_programa', text: 'Se atienen al programa' },
            { name: 'pq1_3_entusiasmo', text: 'Trasmiten entusiasmo y motivación' },
            { name: 'pq1_4_participativas', text: 'Clases participativas' },
            { name: 'pq1_5_ritmo_clase', text: 'Revisa avances y adecua ritmo' },
            { name: 'pq1_6_preparacion_clases', text: 'Preparación y conocimiento de la materia' },
            { name: 'pq1_7_comportamiento_profesor', text: 'Comportamiento y actitud correctos' }
        ];
        pq1_items.forEach(item => {
            let val = formData.get(item.name);
            let score = getScore(val);
            blockScores["Valoración Profesorado"].score += score;
            blockScores["Valoración Profesorado"].maxScore += maxPointsPerQuestion;
            blockScores["Valoración Profesorado"].questions.push(`   - ${item.text}: ${val || 'No respondido'} (${score} pts)`);
        });

        if (blockScores["Valoración Profesorado"].questions.length > 0) {
            report += "1. Nivel de valoración del profesorado:\n";
            report += blockScores["Valoración Profesorado"].questions.join('\n') + '\n';
        }

        report += `2. Aspectos a mejorar (Profesor): \n   ${formData.get('pq2_mejorar_profesor') || 'Sin comentarios'}\n\n`;

        // --- Summary of Block Scores ---
        report += "--- RESUMEN DE PUNTUACIONES POR BLOQUE ---\n";
        const chartLabels = [];
        const chartData = [];
        const chartBackgroundColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];
        const chartBorderColors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];

        let colorIndex = 0;
        for (const blockName in blockScores) {
            if (blockScores[blockName].maxScore > 0) { // Only include blocks with scorable questions
                report += `${blockName}: ${blockScores[blockName].score} / ${blockScores[blockName].maxScore} pts\n`;
                chartLabels.push(blockName);
                chartData.push(blockScores[blockName].score);
            }
        }

        reportContentPre.textContent = report;
        reportOutputDiv.style.display = 'block';

        // --- Render Chart ---
        if (myScoreChart) {
            myScoreChart.destroy(); // Destroy previous chart instance
        }
        myScoreChart = new Chart(chartCanvas, {
            type: 'pie', // 'pie' or 'doughnut'
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Puntuación por Bloque',
                    data: chartData,
                    backgroundColor: chartBackgroundColors.slice(0, chartData.length),
                    borderColor: chartBorderColors.slice(0, chartData.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Puntuaciones por Bloque'
                    }
                }
            }
        });
        
        reportOutputDiv.scrollIntoView({ behavior: 'smooth' });
    });

    form.addEventListener('reset', function() {
        reportOutputDiv.style.display = 'none';
        reportContentPre.textContent = '';
        if (myScoreChart) {
            myScoreChart.destroy();
            myScoreChart = null;
        }
        document.querySelectorAll('.likert-scale label.selected-label').forEach(label => {
            label.classList.remove('selected-label');
        });
    });
});