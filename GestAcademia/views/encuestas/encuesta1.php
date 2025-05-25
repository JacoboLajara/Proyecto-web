<?php
// views/encuestas/encuesta1.php
// Espera que el controlador haya definido $datos con:
//   $datos['ID_Curso'], $datos['curso'], $datos['ID_Profesor'], $datos['profesor']
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de Satisfacción</title>
    <link rel="stylesheet" href="../../CSS/encuesta1.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <header>
            <h1>EVALUACIÓN DEL NIVEL DE SATISFACCIÓN DEL ALUMNO</h1>
            <p>
                SELECT LOGICAL, S. L. está implicada en un proceso de mejora continua en la calidad del servicio
                que ofrece a sus alumnos, de ahí que sea importante para nosotros conocer su grado de satisfacción.
            </p>
        </header>

        <form id="satisfactionForm" action="/router.php?route=encuestas" method="post">
            <!-- Datos de contexto -->
            <section class="form-info">
                <div class="info-item">
                    <label>Curso sobre el que responde:</label>
                    <input type="text" value="<?= htmlspecialchars($datos['curso']) ?>" disabled>
                    <input type="hidden" name="courseId" value="<?= htmlspecialchars($datos['ID_Curso']) ?>">
                </div>
                <div class="info-item">
                    <label>Profesor evaluado:</label>
                    <input type="text" value="<?= htmlspecialchars($datos['profesor']) ?>" disabled>
                    <input type="hidden" name="profesorId" value="<?= htmlspecialchars($datos['ID_Profesor']) ?>">
                </div>
                <div class="info-item">
                    <label for="responseDate">Fecha de la respuesta:</label>
                    <input type="text" id="responseDate" name="responseDate" value="<?= date('d/m/Y') ?>" readonly>
                </div>

            </section>

            <p class="note">Nota: Seleccione la opción correspondiente para cada pregunta.</p>

            <!-- ENCUESTA CENTRO -->
            <fieldset>
                <legend>EVALUACIÓN DE SELECT LOGICAL, S.L.</legend>

                <!-- 1. Valoración global -->
                <div class="question-group">
                    <p class="question-main">1. Valoración global del curso:</p>
                    <div class="likert-scale">
                        <?php foreach (['Excelente', 'Bueno', 'Correcto', 'Regular', 'Deficiente'] as $opt): ?>
                            <label>
                                <input type="radio" name="q1_valoracion_global" value="<?= $opt ?>" required>
                                <?= $opt ?> <span
                                    class="icon"><?php switch ($opt) {
                                        case 'Excelente':
                                            echo '😊';
                                            break;
                                        case 'Bueno':
                                            echo '🙂';
                                            break;
                                        case 'Correcto':
                                            echo '😐';
                                            break;
                                        case 'Regular':
                                            echo '😕';
                                            break;
                                        case 'Deficiente':
                                            echo '😠';
                                            break;
                                    } ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Sub-preguntas 2.1–2.5 -->
                <?php
                $preg2 = [
                    'q2_1_expectativas' => 'El programa del curso cubre mis expectativas',
                    'q2_2_material' => 'La entrega y presentación del material es adecuada',
                    'q2_3_conocimientos' => 'Los contenidos han ampliado mis conocimientos',
                    'q2_4_equipos' => 'Los equipos son prácticos',
                    'q2_5_medios' => 'Variedad de medios didácticos'
                ];
                foreach ($preg2 as $name => $label): ?>
                    <div class="question-group">
                        <p class="question-main"><?= $label ?>:</p>
                        <div class="likert-scale">
                            <?php foreach (['Excelente', 'Bueno', 'Correcto', 'Regular', 'Deficiente'] as $opt): ?>
                                <label>
                                    <input type="radio" name="<?= $name ?>" value="<?= $opt ?>" required>
                                    <span
                                        class="icon"><?php switch ($opt) {
                                            case 'Excelente':
                                                echo '😊';
                                                break;
                                            case 'Bueno':
                                                echo '🙂';
                                                break;
                                            case 'Correcto':
                                                echo '😐';
                                                break;
                                            case 'Regular':
                                                echo '😕';
                                                break;
                                            case 'Deficiente':
                                                echo '😠';
                                                break;
                                        } ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- 3. Personal e instalaciones -->
                <?php
                $preg3 = [
                    'q3_1_info_doc' => 'Información y documentación',
                    'q3_2_personal_serv' => 'Trato del personal de servicios',
                    'q3_3_instalaciones' => 'Instalaciones'
                ];
                foreach ($preg3 as $name => $label): ?>
                    <div class="question-group">
                        <p class="question-main"><?= $label ?>:</p>
                        <div class="likert-scale">
                            <?php foreach (['Excelente', 'Bueno', 'Correcto', 'Regular', 'Deficiente'] as $opt): ?>
                                <label>
                                    <input type="radio" name="<?= $name ?>" value="<?= $opt ?>" required>
                                    <span
                                        class="icon"><?php switch ($opt) {
                                            case 'Excelente':
                                                echo '😊';
                                                break;
                                            case 'Bueno':
                                                echo '🙂';
                                                break;
                                            case 'Correcto':
                                                echo '😐';
                                                break;
                                            case 'Regular':
                                                echo '😕';
                                                break;
                                            case 'Deficiente':
                                                echo '😠';
                                                break;
                                        } ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- 4. Comparación con competidores -->
                <div class="question-group">
                    <p class="question-main">4. Satisfacción comparada con competidores:</p>
                    <div class="likert-scale">
                        <?php foreach (['Excelente', 'Bueno', 'Correcto', 'Regular', 'Deficiente'] as $opt): ?>
                            <label>
                                <input type="radio" name="q4_competidores" value="<?= $opt ?>" required>
                                <?= $opt ?> <span
                                    class="icon"><?php switch ($opt) {
                                        case 'Excelente':
                                            echo '😊';
                                            break;
                                        case 'Bueno':
                                            echo '🙂';
                                            break;
                                        case 'Correcto':
                                            echo '😐';
                                            break;
                                        case 'Regular':
                                            echo '😕';
                                            break;
                                        case 'Deficiente':
                                            echo '😠';
                                            break;
                                    } ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 5. Comentario -->
                <div class="question-group">
                    <label class="question-main" for="q5_mejorar_select">5. Aspectos que deberíamos mejorar:</label>
                    <textarea id="q5_mejorar_select" name="q5_mejorar_select" rows="4" required></textarea>
                </div>

                <!-- 6. Comentario -->
                <div class="question-group">
                    <label class="question-main" for="q6_sugerencias_select">6. Sugerencias para mejora
                        continua:</label>
                    <textarea id="q6_sugerencias_select" name="q6_sugerencias_select" rows="4" required></textarea>
                </div>
            </fieldset>

            <!-- ENCUESTA PROFESOR -->
            <fieldset>
                <legend>EVALUACIÓN DEL PROFESORADO</legend>
                <div class="question-group">
                    <p class="question-main">1. Nivel de valoración del profesorado:</p>
                    <?php
                    $pregProf = [
                        'pq1_1_claridad' => 'Claridad de exposición',
                        'pq1_2_atienen_programa' => 'Se ajusta al programa',
                        'pq1_3_entusiasmo' => 'Entusiasmo',
                        'pq1_4_participativas' => 'Dinámica participativa',
                        'pq1_5_ritmo_clase' => 'Ritmo de clase',
                        'pq1_6_preparacion_clase' => 'Preparación de la clase',
                        'pq1_7_comportamiento_profesor' => 'Comportamiento profesional'
                    ];
                    foreach ($pregProf as $name => $label): ?>
                        <div class="sub-question">
                            <p>- <?= $label ?>:</p>
                            <div class="likert-scale">
                                <?php foreach (['Excelente', 'Bueno', 'Correcto', 'Regular', 'Deficiente'] as $opt): ?>
                                    <label>
                                        <input type="radio" name="<?= $name ?>" value="<?= $opt ?>" required>
                                        <span
                                            class="icon"><?php switch ($opt) {
                                                case 'Excelente':
                                                    echo '😊';
                                                    break;
                                                case 'Bueno':
                                                    echo '🙂';
                                                    break;
                                                case 'Correcto':
                                                    echo '😐';
                                                    break;
                                                case 'Regular':
                                                    echo '😕';
                                                    break;
                                                case 'Deficiente':
                                                    echo '😠';
                                                    break;
                                            } ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="question-group">
                        <label class="question-main" for="pq2_mejorar_profesor">2. Aspectos a mejorar del
                            profesor:</label>
                        <textarea id="pq2_mejorar_profesor" name="pq2_mejorar_profesor" rows="4" required></textarea>
                    </div>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit">Enviar Encuesta</button>
                <button type="reset">Limpiar Formulario</button>
            </div>
        </form>

        <div id="reportOutput" class="report-output" style="display:none;">
            <h2>Informe de Respuestas y Puntuaciones</h2>
            <pre id="reportContent"></pre>
            <div class="chart-container" style="position: relative; height:40vh; width:80vw; margin: 20px auto;">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
    </div>
    <script src="../../js/encuesta1.js"></script>
</body>

</html>