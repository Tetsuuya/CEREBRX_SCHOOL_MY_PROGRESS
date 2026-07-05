<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffeef0 0%, #fff5f6 50%, #ffffff 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .session-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(220, 53, 69, 0.1);
        }

        .session-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(220, 53, 69, 0.15);
        }

        .session-header {
            background: linear-gradient(135deg, #f8f9fa, #fff);
            padding: 1.5rem 2rem;
            border-bottom: 2px solid rgba(220, 53, 69, 0.1);
            position: relative;
        }

        .session-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #dc3545, #e74c3c, #dc3545);
        }

        .session-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-container {
            padding: 0;
            overflow-x: auto;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .grades-table th {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 1rem 0.75rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            position: relative;
        }

        .grades-table th:first-child {
            text-align: left;
            padding-left: 1.5rem;
        }

        .grades-table td {
            padding: 0.875rem 0.75rem;
            text-align: center;
            border-bottom: 1px solid rgba(220, 53, 69, 0.1);
            transition: background-color 0.2s ease;
        }

        .grades-table td:first-child {
            text-align: left;
            padding-left: 1.5rem;
            font-weight: 500;
            color: #2c3e50;
        }

        .grades-table tbody tr:hover {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .grades-table tbody tr:nth-child(even) {
            background-color: rgba(248, 249, 250, 0.8);
        }

        .average-row {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05)) !important;
            font-weight: 600;
        }

        .average-row td, .average-row th {
            border-top: 2px solid #dc3545;
            padding: 1rem 0.75rem;
            color: #dc3545;
        }

        .average-row th {
            color: white !important;
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .grade-cell {
            font-weight: 500;
            min-width: 60px;
        }

        .grade-cell:not(:empty) {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            border-radius: 6px;
            color: #28a745;
            font-weight: 600;
        }

        .final-grade {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05)) !important;
            color: #dc3545 !important;
            font-weight: 700 !important;
            border-radius: 6px;
        }

        .student-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .student-id {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .session-header {
                padding: 1rem 1.5rem;
            }
            
            .grades-table {
                font-size: 0.85rem;
            }
            
            .grades-table th,
            .grades-table td {
                padding: 0.75rem 0.5rem;
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer-loading 2s infinite;
        }

        @keyframes shimmer-loading {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Student Academic Report</h1>
            <div class="student-info">
                <div style="text-align: center;">
                    <div style="margin-bottom: 0.5rem;">
                        <span class="subtitle">Student Name:</span>
                        <span class="student-id" style="font-weight: bold;">
                            <?= htmlspecialchars($student_data['firstname']) ?>
                            <?= !empty($student_data['middlename']) ? ' ' . htmlspecialchars(substr($student_data['middlename'], 0, 1)) . '.' : '' ?>
                            <?= ' ' . htmlspecialchars($student_data['lastname']) ?>
                        </span>
                    </div>
                    <div>
                        <span class="subtitle">Student LRN:</span>
                        <span class="student-id"><?= htmlspecialchars($student_data['lrn']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($sessions)): ?>
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>No Academic Records Found</h3>
                <p>There are currently no grade records available for this student.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($sessions as $session): ?>
            <div class="session-card">
                <div class="session-header">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        <?= 'SY ' . htmlspecialchars($session['session_name']) . ' - ' . htmlspecialchars($session['class_name']) . ' - ' . htmlspecialchars($session['section_name']) ?>
                    </h2>
                </div>

                <div class="table-container">
                    <?php
                    $student_class = strtolower(trim($student_data['class']));
                    $is_senior_high = in_array($student_class, ['grade 11', 'grade 12']);
                    ?>

                    <?php if (!$is_senior_high): ?>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Term 1</th>
                                    <th>Term 2</th>
                                    <th>Term 3</th>
                                    <th>Final</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_per_q = [1 => 0, 2 => 0, 3 => 0];
                            $final_total = 0;
                            $subject_count = 0;
                            ?>
                            <?php foreach ($session['subjects'] as $subject): ?>
                                <tr>
                                    <td><?= htmlspecialchars($subject['name']) ?></td>
                                    <?php
                                    $has_grades = false;
                                    for ($q = 1; $q <= 3; $q++): 
                                        $grade = isset($subject['grades'][$q]) ? $subject['grades'][$q] : null;
                                        if (!empty($grade)) {
                                            $total_per_q[$q] += $grade;
                                            $has_grades = true;
                                        }
                                    ?>
                                        <td<?= (!empty($grade) && is_numeric($grade) && $grade < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($grade) ? htmlspecialchars($grade) : '-' ?></td>
                                    <?php endfor; ?>
                                    <td>
                                        <?php
                                        if (!empty($subject['final_grade'])) {
                                            $final_total += $subject['final_grade'];
                                        }
                                        $final_g = !empty($subject['final_grade']) ? htmlspecialchars($subject['final_grade']) : '-';
                                        $final_style = (!empty($subject['final_grade']) && is_numeric($subject['final_grade']) && $subject['final_grade'] < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                        ?>
                                        <span<?= $final_style ?>><?= $final_g ?></span>
                                    </td>
                                </tr>
                                <?php if ($has_grades) $subject_count++; ?>
                            <?php endforeach; ?>
                            <tr style="font-weight: bold; background-color: #f9f9f9;">
                                <td>General Average</td>
                                <?php for ($q = 1; $q <= 3; $q++): 
                                    $avg_val = $subject_count ? round($total_per_q[$q] / $subject_count, 2) : '-';
                                    $avg_style = ($subject_count && is_numeric($avg_val) && $avg_val < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                ?>
                                    <td<?= $avg_style ?>><?= $avg_val ?></td>
                                <?php endfor; ?>
                                <?php
                                $final_avg_val = $subject_count ? round($final_total / $subject_count, 2) : '-';
                                $final_avg_style = ($subject_count && is_numeric($final_avg_val) && $final_avg_val < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                ?>
                                <td<?= $final_avg_style ?>><?= $final_avg_val ?></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <h4>First Semester</h4>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Term 1</th>
                                    <th>Term 2</th>
                                    <th>Final</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_q1 = 0; $total_q2 = 0; $total_final = 0; $count = 0;
                            ?>
                            <?php foreach ($session['subjects'] as $subject): ?>
                                <?php if ($subject['semester'] == 1): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subject['name']) ?></td>
                                        <?php
                                        $q1 = isset($subject['grades'][1]) ? $subject['grades'][1] : null;
                                        $q2 = isset($subject['grades'][2]) ? $subject['grades'][2] : null;
                                        $final = $subject['final_grade'] ? $subject['final_grade'] : null;

                                        if (!empty($q1)) $total_q1 += $q1;
                                        if (!empty($q2)) $total_q2 += $q2;
                                        if (!empty($final)) $total_final += $final;

                                        if (!empty($q1) || !empty($q2) || !empty($final)) $count++;
                                        ?>
                                        <td<?= (!empty($q1) && is_numeric($q1) && $q1 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($q1) ? $q1 : '-' ?></td>
                                        <td<?= (!empty($q2) && is_numeric($q2) && $q2 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($q2) ? $q2 : '-' ?></td>
                                        <td<?= (!empty($final) && is_numeric($final) && $final < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($final) ? $final : '-' ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr style="font-weight: bold; background-color: #f9f9f9;">
                                <td>General Average</td>
                                <?php
                                $avg_q1 = $count ? round($total_q1 / $count, 2) : '-';
                                $avg_q1_style = ($count && is_numeric($avg_q1) && $avg_q1 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                $avg_q2 = $count ? round($total_q2 / $count, 2) : '-';
                                $avg_q2_style = ($count && is_numeric($avg_q2) && $avg_q2 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                $avg_final = $count ? round($total_final / $count, 2) : '-';
                                $avg_final_style = ($count && is_numeric($avg_final) && $avg_final < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                ?>
                                <td<?= $avg_q1_style ?>><?= $avg_q1 ?></td>
                                <td<?= $avg_q2_style ?>><?= $avg_q2 ?></td>
                                <td<?= $avg_final_style ?>><?= $avg_final ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <h4>Second Semester</h4>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Term 3</th>
                                    <th>Final</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_q3 = 0; $total_final = 0; $count = 0;
                            ?>
                            <?php foreach ($session['subjects'] as $subject): ?>
                                <?php if ($subject['semester'] == 2): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subject['name']) ?></td>
                                        <?php
                                        $q3 = isset($subject['grades'][3]) ? $subject['grades'][3] : null;
                                        $final = $subject['final_grade'] ? $subject['final_grade'] : null;

                                        if (!empty($q3)) $total_q3 += $q3;
                                        if (!empty($final)) $total_final += $final;

                                        if (!empty($q3) || !empty($final)) $count++;
                                        ?>
                                        <td<?= (!empty($q3) && is_numeric($q3) && $q3 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($q3) ? $q3 : '-' ?></td>
                                        <td<?= (!empty($final) && is_numeric($final) && $final < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '' ?>><?= !empty($final) ? $final : '-' ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr style="font-weight: bold; background-color: #f9f9f9;">
                                <td>General Average</td>
                                <?php
                                $avg_q3 = $count ? round($total_q3 / $count, 2) : '-';
                                $avg_q3_style = ($count && is_numeric($avg_q3) && $avg_q3 < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                $avg_final = $count ? round($total_final / $count, 2) : '-';
                                $avg_final_style = ($count && is_numeric($avg_final) && $avg_final < 75) ? ' style="color: #dc3545; font-weight: bold;"' : '';
                                ?>
                                <td<?= $avg_q3_style ?>><?= $avg_q3 ?></td>
                                <td<?= $avg_final_style ?>><?= $avg_final ?></td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.session-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>