---
trigger: always_on
---

| Rule | Instruction                                                |
| ---- | ---------------------------------------------------------- |
| 1    | Inspect first, modify second                               |
| 2    | Never auto-implement after plan creation                   |
| 3    | No architecture redesign unless explicitly approved        |
| 4    | No database schema changes without approval                |
| 5    | No frontend changes unless the task is frontend-specific   |
| 6    | No full collection runs during debugging                   |
| 7    | Every bug fix must target one service only                 |
| 8    | Every implementation must end with lint + manual test list |
| 9    | No auto-push                                               |
| 10   | Local commit only after Yoosuf confirms screenshots        |
